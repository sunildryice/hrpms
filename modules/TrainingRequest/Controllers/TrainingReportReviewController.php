<?php

namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Notifications\TrainingReportApproved;
use Modules\TrainingRequest\Notifications\TrainingReportRecommended;
use Modules\TrainingRequest\Notifications\TrainingReportRejected;
use Modules\TrainingRequest\Repositories\TrainingReportRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\TrainingRequest\Repositories\TrainingReportQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TrainingRequest\Requests\TrainingReportApproval\StoreRequest;
// use Modules\TrainingRequest\Requests\TrainingReport\UpdateRequest;

use DataTables;
use DB;

class TrainingReportReviewController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository                $employees,
     * @param RoleRepository                    $roles,
     * @param TrainingReportRepository          $trainingReport,
     * @param TrainingRequestRepository         $trainingRequest,
     * @param TrainingReportQuestionRepository  $trainingReportQuestion,
     * @param TrainingRequestQuestionRepository $trainingRequestQuestion,
     * @param UserRepository                    $user
     *
     */
    public function __construct(
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingReportRepository          $trainingReport,
        TrainingRequestRepository         $trainingRequest,
        TrainingReportQuestionRepository  $trainingReportQuestion,
        TrainingRequestQuestionRepository  $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->employees                  = $employees;
        $this->roles                      = $roles;
        $this->trainingReport             = $trainingReport;
        $this->trainingRequest            = $trainingRequest;
        $this->trainingReportQuestion     = $trainingReportQuestion;
        $this->trainingRequestQuestion    = $trainingRequestQuestion;
        $this->user                       = $user;
        $this->destinationPath            = 'trainingReport';
    }

     /**
     * Display a listing of the training report
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
//        $this->authorize('manage-employee');
       $authUser = auth()->user();
        // $this->authorize('training-report');
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingReport
                        ->select(['*'])
                        ->with('trainingRequest')
                        ->where(function ($q) use ($userId) {
                            $q->where('reviewer_id', $userId);
                            $q->where('status_id', 3);
                        })
                        ->orWhere(function ($q) use ($userId) {
                            $q->where('approver_id', $userId);
                            $q->where('status_id', 4);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
            return DataTables::of($data)
                ->addIndexColumn()->addColumn('training_number', function ($row){
                    return $row->trainingRequest->getTrainingRequestNumber();
                })->addColumn('name_of_course', function ($row){
                    return $row->trainingRequest->title;
                })->addIndexColumn()->addColumn('duration', function ($row){
                    return $row->trainingRequest->getDuration();
                })->addColumn('remarks', function ($row){
                    return $row->trainingRequest->description;
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if($authUser->can('approve', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approve.training.reports.create', $row->id) . '" rel="tooltip" title="Approve"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::TrainingReportApproval.index');
    }

    /**
     * Show the form for creating a new Training report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
       // $this->authorize('training-report');
       $authUser = auth()->user();
       $trainingReport = $this->trainingReport->find($id);
       $this->authorize('approve', $trainingReport);
       $trainingRequest = $this->trainingRequest->find($trainingReport->training_id);
        if($trainingReport){
            $trainingRequestQuestion = $this->trainingRequestQuestion
                                            ->select('*')
                                            ->with('trainingQuestion')
                                            ->where('training_id', $trainingReport->training_id)
                                            ->orderBy('question_id','asc')
                                            ->get();
            $trainingReportQuestion = $this->trainingReportQuestion
                                            ->select('*')
                                            ->with('trainingQuestion')
                                            ->where('training_report_id', $id)
                                            ->orderBy('question_id','asc')
                                            ->get();
            $approvers = $this->user->permissionBasedUsers('approve-training-report');

            return view('TrainingRequest::TrainingReportApproval.create')
                ->withAuthUser($authUser)
                ->withApprovers($approvers)
                ->withTrainingReport($trainingReport)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingRequestQuestions($trainingRequestQuestion)
                ->withTrainingReportQuestions($trainingReportQuestion)
                ->withRoles($this->roles->get());
        }
    }

    /**
     * Store a newly created Training report in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $trainingReport = $this->trainingReport->find($id);
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['training_report_id'] = $trainingReport->id;
        $inputs['updated_by'] = $userId;
        $inputs['user_id'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['approver_id'] = $inputs['status_id'] == 6 || $inputs['status_id'] == 2 || $inputs['status_id'] == 8?$userId:$inputs['approver_id'];
        $trainingReport = $this->trainingReport->approve($id, $inputs);

        if ($trainingReport) {
            if($trainingReport->status_id == 4){
                $trainingReport->approver->notify(new TrainingReportRecommended($trainingReport));
                $message = 'Training Report is successfully recommended.';
            }elseif($trainingReport->status_id == 6){
                $trainingReport->createdBy->notify(new TrainingReportApproved($trainingReport));
                $message = 'Training Report is successfully approved.';
            }elseif($trainingReport->status_id == 8){
                $trainingReport->createdBy->notify(new TrainingReportRejected($trainingReport));
                $message = 'Training Report is successfully rejected.';
            }else{
                $message = 'Training Report status is successfully updated.';
            }

            return redirect()->route('approve.training.reports.index')
            ->withSuccessMessage($message);
        }

        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training Report can not be approved.');

    }
}
