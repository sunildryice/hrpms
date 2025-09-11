<?php

namespace Modules\TrainingRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\TrainingQuestionRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TrainingRequest\Repositories\TrainingReportQuestionRepository;
use Modules\TrainingRequest\Repositories\TrainingReportRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestQuestionRepository;
use Modules\TrainingRequest\Repositories\TrainingRequestRepository;

class ApprovedTrainingReportController extends Controller
{
    /**
     * Create a new controller instance.
     * @param EmployeeRepository $employees ,
     * @param RoleRepository $roles ,
     * @param TrainingQuestionRepository $trainingQuestion ,
     * @param TrainingReportRepository $trainingReport ,
     * @param TrainingRequestRepository $trainingRequest ,
     * @param TrainingReportQuestionRepository $trainingReportQuestion ,
     * @param TrainingRequestQuestionRepository $trainingRequestQuestion ,
     * @param UserRepository $user
     *
     */
    public function __construct(
        EmployeeRepository                $employees,
        RoleRepository                    $roles,
        TrainingQuestionRepository        $trainingQuestion,
        TrainingReportRepository          $trainingReport,
        TrainingRequestRepository         $trainingRequest,
        TrainingReportQuestionRepository  $trainingReportQuestion,
        TrainingRequestQuestionRepository $trainingRequestQuestion,
        UserRepository                    $user
    )
    {
        $this->employees = $employees;
        $this->roles = $roles;
        $this->trainingQuestion = $trainingQuestion;
        $this->trainingReport = $trainingReport;
        $this->trainingRequest = $trainingRequest;
        $this->trainingReportQuestion = $trainingReportQuestion;
        $this->trainingRequestQuestion = $trainingRequestQuestion;
        $this->user = $user;
        $this->destinationPath = 'trainingRequest';
    }

    /**
     * Display a listing of the payment sheets
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $userId = auth()->id();
        if ($request->ajax()) {
            $data = $this->trainingReport
                ->select(['*'])
                ->with('trainingRequest')
                ->where('status_id', '6')
                // ->where(function ($q) use ($userId) {
                //     $q->where('reviewer_id', $userId);
                //     $q->orwhere('approver_id', $userId);
                //     $q->orwhere('created_by', $userId);
                // })
                ->orderBy('created_at', 'desc')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()->addColumn('training_number', function ($row) {
                    return $row->trainingRequest->getTrainingRequestNumber();
                })->addColumn('name_of_course', function ($row) {
                    return $row->trainingRequest->title;
                })->addIndexColumn()->addColumn('duration', function ($row) {
                    return $row->trainingRequest->getDuration();
                })->addColumn('remarks', function ($row) {
                    return $row->trainingRequest->description;
                })->addColumn('requester', function ($row) {
                    return $row->trainingRequest->requester->getFullName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.training.report.show', $row->id) . '" rel="tooltip" title="View Training report">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('print', $row) || $authUser->can('hr-review-training-request')) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('training.report.print', $row->id) . '" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('TrainingRequest::ApprovedReport.index');
    }

    /**
     * Show the specified payment sheet.
     *
     * @param $paymentRequestId
     * @return mixed
     */
    public function show($id)
    {
        $authUser = auth()->user();
        $trainingReport = $this->trainingReport->find($id);
        $this->authorize('viewApproved', $trainingReport);
        $trainingRequest = $this->trainingRequest->find($trainingReport->training_id);
        if ($trainingReport) {
            $trainingRequestQuestion = $this->trainingRequestQuestion
                ->select('*')
                ->with('trainingQuestion')
                ->where('training_id', $trainingReport->training_id)
                ->orderBy('question_id', 'asc')
                ->get();
            $trainingReportQuestion = $this->trainingReportQuestion
                ->select('*')
                ->with('trainingQuestion')
                ->where('training_report_id', $id)
                ->orderBy('question_id', 'asc')
                ->get();
            $duration = $trainingRequest->getStartDate() . ' - ' . $trainingRequest->getEndDate();

            return view('TrainingRequest::ApprovedReport.show')
                ->withAuthUser($authUser)
                ->withDuration($duration)
                ->withTrainingReport($trainingReport)
                ->withTrainingRequest($trainingRequest)
                ->withTrainingRequestQuestions($trainingRequestQuestion)
                ->withTrainingReportQuestions($trainingReportQuestion);
        }
    }

    /**
     * Show the specified training report in printable view
     *
     * @param $id
     * @return mixed
     */
    public function print($id)
    {
        $authUser = auth()->user();
        $trainingReport = $this->trainingReport->find($id);
        $this->authorize('viewApproved', $trainingReport);
        $trainingReport = $this->trainingReport->select('*')
            ->with('trainingRequest', 'logs', 'createdBy', 'reviewer', 'approver')
            ->where('id', $id)
            ->where('status_id', '6')
            ->first();
        $requester = $this->employees->select('*')->where('id', $trainingReport->createdBy->employee_id)->first();
        $reviewer = $this->employees->select('*')->where('id', $trainingReport->reviewer->employee_id)->first();
        $approver = $this->employees->select('*')->where('id', $trainingReport->approver->employee_id)->first();
        $date = array();
        foreach ($trainingReport->logs as $log) {
            if ($log->status_id == 3) {
                $date['submitted_date'] = $log->created_at;
            }
            if ($log->status_id == 4) {
                $date['reviewed_date'] = $log->created_at;
            }
            if ($log->status_id == 6) {
                $date['approved_date'] = $log->created_at;
            }

        }
        $trainingReportQuestion = $this->trainingReportQuestion
            ->select('*')
            ->with('trainingQuestion')
            ->where('training_report_id', $id)
            ->orderBy('question_id', 'asc')
            ->get();
        return view('TrainingRequest::ApprovedReport.print')
            ->withApprover($approver)
            ->withDates($date)
            ->withRequester($requester)
            ->withReviewer($reviewer)
            ->withTrainingReport($trainingReport)
            ->withTrainingReportQuestions($trainingReportQuestion);
    }
}
