<?php

namespace Modules\TrainingRequest\Controllers;

use DB;
use DataTables;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\TrainingRequest\Notifications\TrainingRequestReturned;

use Modules\TrainingRequest\Repositories\TrainingRequestRepository;

use Modules\TrainingRequest\Requests\TrainingResponse\StoreRequest;
use Modules\TrainingRequest\Notifications\TrainingRequestRecommended;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\TrainingRequest\Notifications\TrainingRequestRecommendedToApprover;

class TrainingResponseController extends Controller
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
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingRequest
                        ->select(['*'])
                        ->where('status_id', '3')
                        ->where(function ($q) use ($userId) {
                            $q->where('reviewer_id', $userId);
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
                    if($authUser->can('review', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('reponses.training.request.create', $row->id) . '" rel="tooltip" title="Fill Response"><i class="bi-list-columns-reverse"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::TrainingResponse.index');
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
        $this->authorize('review', $trainingRequest);
        $trainingRequestQuestion = $this->trainingRequestQuestion
                                        ->select('*')
                                        ->with('trainingQuestion')
                                        ->where('training_id', $id)
                                        ->orderBy('question_id','asc')
                                        ->get();
        $supervisors = $this->user->getSupervisors($trainingRequest->requester);

        return view('TrainingRequest::TrainingResponse.create')
            ->withAuthUser($authUser)
            ->withSupervisors($supervisors)
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
        $this->authorize('review', $trainingRequest);
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        if($inputs['status_id'] == config('constant.RETURNED_STATUS')){
            $trainingRequest = $this->trainingRequest->approve($inputs,$id);
            if($trainingRequest){
                $trainingRequest->createdBy->notify(new TrainingRequestReturned($trainingRequest));
                return redirect()->route('reponses.training.request.index')->withSuccessMessage('Training request is returned.');
            }
        }

        $selfApproval = $trainingRequest->created_by == $trainingRequest->approver_id;

        if ($selfApproval || isset($trainingRequest->approver_id)) {
            $inputs['status_id'] = config('constant.RECOMMENDED2_STATUS');
        }
        $trainingRequest = $this->trainingRequest->addResponse($inputs, $id);

        if ($trainingRequest) {
            $message = 'Training Response is successfully added.';
            if ($selfApproval || isset($trainingRequest->approver_id)) {
                $trainingRequest->approver->notify(new TrainingRequestRecommendedToApprover($trainingRequest));
            } else {
                $trainingRequest->recommender->notify(new TrainingRequestRecommended($trainingRequest));
            }
            return redirect()->route('reponses.training.request.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training Request Details can not be edited.');
    }
}
