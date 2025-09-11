<?php

namespace Modules\TrainingRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\TrainingRequest\Notifications\TrainingReportSubmitted;
use Modules\TrainingRequest\Repositories\TrainingReportRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;
use Modules\TrainingRequest\Repositories\TrainingReportQuestionRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TrainingRequest\Requests\TrainingReport\StoreRequest;
// use Modules\TrainingRequest\Requests\TrainingReport\UpdateRequest;

use DataTables;
use DB;

class TrainingReportController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository                $employees,
     * @param RoleRepository                    $roles,
     * @param TrainingQuestionRepository        $trainingQuestion,
     * @param TrainingReportRepository          $trainingReport,
     * @param TrainingRequestRepository         $trainingRequest,
     * @param TrainingReportQuestionRepository  $trainingReportQuestion,
     * @param TrainingRequestQuestionRepository  $trainingRequestQuestion,
     * @param UserRepository                    $user
     *
     */
    public function __construct(
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingQuestionRepository        $trainingQuestion,
        TrainingReportRepository          $trainingReport,
        TrainingRequestRepository         $trainingRequest,
        TrainingReportQuestionRepository  $trainingReportQuestion,
        TrainingRequestQuestionRepository  $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->employees                  = $employees;
        $this->roles                      = $roles;
        $this->trainingQuestion           = $trainingQuestion;
        $this->trainingReport             = $trainingReport;
        $this->trainingRequest            = $trainingRequest;
        $this->trainingReportQuestion     = $trainingReportQuestion;
        $this->trainingRequestQuestion     = $trainingRequestQuestion;
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
                        ->where('created_by', $userId)
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
                    $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('training.report.view', $row->id) . '" rel="tooltip" title="View"><i class="bi-eye"></i></a>';
                    if($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.report.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::TrainingReport.index');
    }

    /**
     * Show the form for creating a new Training report by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($trainingRequestId)
    {
       // $this->authorize('training-report');
       $authUser = auth()->user();
       $userId = auth()->id();
       $trainingRequest = $this->trainingRequest->find($trainingRequestId);
       $trainingReport = $this->trainingReport->select('*')->where('training_id', $trainingRequestId)->first();
    //    $reviewers = $this->user->permissionBasedUsers('approve-training-request');
       $reviewers = $this->user->getSupervisors($trainingRequest->requester);
        if($trainingReport){
            $id = $trainingReport->id;
            $trainingReportQuestion = $this->trainingReportQuestion
                                            ->where('training_report_id','=', $id)
                                            ->orderby('id', 'asc')
                                            ->get();
            if(in_array($trainingReport->status_id, [3,6,4,5,8])){
                return redirect()->route('training.report.view', $id);
            }
            return view('TrainingRequest::TrainingReport.edit')
                ->withAuthUser($authUser)
                ->withReviewers($reviewers)
                ->withTrainingReport($trainingReport)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingReportQuestions($trainingReportQuestion)
                ->withRoles($this->roles->get());
        }else{
            $this->authorize('createReport', $trainingRequest);
            return view('TrainingRequest::TrainingReport.create')
                ->withAuthUser($authUser)
                ->withReviewers($reviewers)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingQuestions($this->trainingQuestion->where('type','=','6')->orderBy('position', 'asc')->get());
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
//        $this->authorize('manage-employee');

        $trainingRequest = $this->trainingRequest->find($id);
        // $this->authorize('createReport', $trainingRequest);
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['training_id'] = $trainingRequest->id;
        $inputs['status_id'] = 1;
        $inputs['created_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['user_id'] = $userId;
        $trainingReport = $this->trainingReport->create($inputs);

        if ($trainingReport) {
            if ($trainingReport->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Training Report is successfully submitted.';
                $trainingReport->reviewer->notify(new TrainingReportSubmitted($trainingReport));
            } else {
                $message = 'Training Report is successfully added.';
            }
            return redirect()->route('training.requests.index')
            ->withSuccessMessage($message);
        }

        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training Report can not be added.');

    }

    /**
     * Store a newly created Training request in storage.
     *
     * @param \Modules\Employee\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreRequest $request, $id)
    {
//        $this->authorize('manage-employee');
        $trainingReport = $this->trainingReport->find($id);
        $trainingRequest = $this->trainingRequest->find($trainingReport->training_id);
        $this->authorize('createReport', $trainingRequest);
        $userId = auth()->id();
        $inputs = $request->validated();
        $inputs['training_id'] = $trainingReport->training_id;
        $inputs['updated_by'] = $userId;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['user_id'] = $userId;
        $trainingReport = $this->trainingReport->update($id, $inputs);

        if ($trainingReport) {
            if ($trainingReport->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Training Report is successfully submitted.';
                $trainingReport->reviewer->notify(new TrainingReportSubmitted($trainingReport));
            } else {
                $message = 'Training Report is successfully updated.';
            }
            return redirect()->route('training.requests.index')
            ->withSuccessMessage($message);
        }

        return redirect()->back()
        ->withInput()
        ->withWarningMessage('Training Report can not be edited.');
    }

    /**
     * View the details the specified Training request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view($id)
    {
//        $this->authorize('manage-employee');
        $authUser = auth()->user();
        $trainingReport = $this->trainingReport->find($id);
        $this->authorize('view', $trainingReport);
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
            return view('TrainingRequest::TrainingReport.view')
                ->withAuthUser($authUser)
                ->withEmployees($this->employees->with('user')->get())
                ->withTrainingReport($trainingReport)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingRequestQuestions($trainingRequestQuestion)
                ->withTrainingReportQuestions($trainingReportQuestion)
                ->withRoles($this->roles->get());
        }
    }

    /**
     * Remove the specified Training request from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $trainingReport = $this->trainingReport->find($id);
        $this->authorize('delete', $trainingReport);
        $flag = $this->trainingReport->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Training Report is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Training Report can not deleted.',
        ], 422);
    }
}
