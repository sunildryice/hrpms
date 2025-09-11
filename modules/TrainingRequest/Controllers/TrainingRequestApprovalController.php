<?php

namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Notifications\TrainingRequestApproved;
use Modules\TrainingRequest\Notifications\TrainingRequestRejected;
use Modules\TrainingRequest\Notifications\TrainingRequestReturned;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TrainingRequest\Requests\TrainingApprove\StoreRequest;

use DataTables;
use DB;


class TrainingRequestApprovalController extends Controller
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
                        ->where('status_id', config('constant.RECOMMENDED2_STATUS'))
                        ->where('approver_id', $userId)
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
                    if($authUser->can('approve', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approve.training.request.create', $row->id) . '" rel="tooltip" title="Approve"><i class="bi-list-columns-reverse"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::TrainingApproval.index');
    }

     /**
     * Show the form for approve by ED.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        // $this->authorize('training-request-response');
        $authUser = auth()->user();
        $trainingRequest = $this->trainingRequest->find($id);
        $this->authorize('approve', $trainingRequest);
        $trainingRequestQuestion = $this->trainingRequestQuestion
                                        ->select('*')
                                        ->with('trainingQuestion')
                                        ->where('training_id', $id)
                                        ->orderBy('question_id','asc')
                                        ->get();
        // $this->authorize('update', $trainingRequest);
        return view('TrainingRequest::TrainingApproval.create')
            ->withAuthUser($authUser)
            ->withEmployees($this->employees->with('user')->get())
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
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $trainingRequest = $this->trainingRequest->approve($inputs, $id);

        if ($trainingRequest) {
            if($trainingRequest['status_id'] == config('constant.APPROVED_STATUS')){
                $trainingRequest->createdBy->notify(new TrainingRequestApproved($trainingRequest));
                $message = 'Training request is approved.';
            }else if($trainingRequest['status_id'] == config('constant.RETURNED_STATUS')){
                $trainingRequest->createdBy->notify(new TrainingRequestReturned($trainingRequest));
                $message = 'Training request is returned.';
            }else{
                $trainingRequest->createdBy->notify(new TrainingRequestRejected($trainingRequest));
                $message = 'Training request is rejected.';
            }
            return redirect()->route('approve.training.requests.index')
            ->withSuccessMessage($message);
        }

        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training response can not be submitted.');
    }

}
