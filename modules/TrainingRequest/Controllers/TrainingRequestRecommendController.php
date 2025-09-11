<?php

namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Notifications\TrainingRequestApproved;
use Modules\TrainingRequest\Notifications\TrainingRequestRecommended;
use Modules\TrainingRequest\Notifications\TrainingRequestRecommendedToApprover;
use Modules\TrainingRequest\Notifications\TrainingRequestReturned;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TrainingRequest\Requests\TrainingRecommend\StoreRequest;

use DataTables;
use DB;

class TrainingRequestRecommendController extends Controller
{
     /**
     * Create a new controller instance.
      *
     * @param EmployeeRepository                $employees,
     * @param RoleRepository                    $roles,
     * @param TrainingQuestionRepository        $trainingQuestion,
     * @param TrainingRequestRepository         $trainingRequest,
     * @param TrainingRequestQuestionRepository $trainingRequestQuestion,
     * @param UserRepository                    $user
     *
     */
    public function __construct(
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingQuestionRepository        $trainingQuestion,
        TrainingRequestRepository         $trainingRequest,
        TrainingRequestQuestionRepository $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->employees                  = $employees;
        $this->roles                      = $roles;
        $this->trainingQuestion           = $trainingQuestion;
        $this->trainingRequest            = $trainingRequest;
        $this->trainingRequestQuestion    = $trainingRequestQuestion;
        $this->user                       = $user;
        $this->destinationPath            = 'trainingRequest';
    }

    /**
     * Display a listing of all the probationary review.
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        // $this->authorize('training-request-response');
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingRequest
                        ->select(['*'])
                        ->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')])
                        ->where(function ($q) use ($userId) {
                            $q->where('recommender_id', $userId);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
            return DataTables::of($data)
                ->addIndexColumn()->addColumn('training_number', function ($row){
                    return $row->getTrainingRequestNumber();
                })->addColumn('name_of_course', function ($row){
                    return $row->title;
                })->addIndexColumn()->addColumn('duration', function ($row){
                    return $row->getDuration();
                })->addColumn('remarks', function ($row){
                    return $row->description;
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if($authUser->can('recommend', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.requests.recommend.create', $row->id) . '" rel="tooltip" title="Recommend"><i class="bi bi-box-arrow-in-up-right"></i></a>';

                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::TrainingRecommend.index');
    }

    /**
     * Show the form for add review detail by supervisor.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('recommend', $trainingRequest);
        $trainingRequestQuestion = $this->trainingRequestQuestion
                                        ->select('*')
                                        ->with('trainingQuestion')
                                        ->where('training_id', $id)
                                        ->orderBy('question_id','asc')
                                        ->get();

        $approvers = $this->user->permissionBasedUsers('approve-training-request');
        $approvers = $approvers->reject(function ($approver) use ($trainingRequest){
            return $trainingRequest->created_by == $approver->id;
        });

        return view('TrainingRequest::TrainingRecommend.create')
            ->withAuthUser($authUser)
            ->withApprovers($approvers)
            ->withTrainingRequest($trainingRequest)
            ->withTrainingRequestQuestions($trainingRequestQuestion)
            ->withTrainingQuestions($this->trainingQuestion->where('type','=','3')->orderBy('position', 'asc')->get());

    }

    /**
     * Store a newly add review details in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('recommend', $trainingRequest);
        $authUser = auth()->user();
        $userId = $authUser->id;
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if($authUser->employee->designation_id == 9 && $inputs['status_id'] == 6){
            $inputs['approver_id'] = $userId;
            $trainingRequest = $this->trainingRequest->approve($inputs, $id);
        }else{
            $trainingRequest = $this->trainingRequest->recommend($inputs, $id);
        }

        if ($trainingRequest) {
            $message = '';
            if ($trainingRequest->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Training Request is successfully approved.';
                $trainingRequest->requester->notify(new TrainingRequestApproved($trainingRequest));
            }else if($trainingRequest->status_id == config('constant.RETURNED_STATUS')){
                $message = 'Training Request is successfully returned.';
                $trainingRequest->requester->notify(new TrainingRequestReturned($trainingRequest));
            }else{
                $message = 'Training Request is successfully recommended.';
                $trainingRequest->approver->notify(new TrainingRequestRecommendedToApprover($trainingRequest));
            }
            return redirect()->route('training.requests.recommend.index')
            ->withSuccessMessage($message);
        }
        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training Request can not be recommended.');
    }
}
