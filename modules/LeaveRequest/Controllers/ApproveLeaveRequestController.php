<?php

namespace Modules\LeaveRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Notifications\LeaveRequestApproved;
use Modules\LeaveRequest\Notifications\LeaveRequestRejected;
use Modules\LeaveRequest\Notifications\LeaveRequestReturned;
use Modules\LeaveRequest\Notifications\LeaveRequestSubmitted;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveModeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\LeaveRequest\Requests\Approve\StoreRequest;
use DataTables;


class ApproveLeaveRequestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LeaveRepository $employeeLeaves
     * @param LeaveRequestRepository $leaveRequests
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository     $employees,
        protected FiscalYearRepository   $fiscalYears,
        protected LeaveRepository        $employeeLeaves,
        protected LeaveModeRepository    $leaveModes,
        protected LeaveRequestRepository $leaveRequests,
        protected UserRepository         $users
    ) {}

    /**
     * Display a listing of the leave requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->leaveRequests->with(['department', 'office', 'leaveType', 'fiscalYear', 'status', 'requester.employee'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->orderBy('start_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('leave_type', function ($row) {
                    return $row->getLeaveType();
                })->addColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })->addColumn('request_days', function ($row) {
                    return $row->getLeaveDuration() . ' ' . $row->leaveType->getLeaveBasis();
                })->addColumn('start_date', function ($row) {
                    return $row->getStartDate();
                })->addColumn('end_date', function ($row) {
                    return $row->getEndDate();
                })->addColumn('leave_number', function ($row) {
                    return $row->getLeaveNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('approve', $row)) {
                        $btn = '<a href="' . route('approve.leave.requests.create', $row->id) . '"' .
                            'class="act-btns bt-primary"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'duration'])
                ->make(true);
        }

        return view('LeaveRequest::Approve.index');
    }

    public function create($leaveRequestId)
    {
        $authUser = auth()->user();
        $leaveRequest = $this->leaveRequests->find($leaveRequestId);
        $this->authorize('approve', $leaveRequest);
        $approverRoles = $leaveRequest->approver->getRoles()->toArray();
        $executiveDirectorflag = in_array('Executive Director', $approverRoles) || $leaveRequest->approver?->employee?->employee_code == 2;

        $leaveDays = $leaveRequest->leaveDays->count();
        if ($leaveRequest->leaveType->leave_basis == 2) {
            $leaveDays = $leaveRequest->leaveDays->sum('leave_duration');
            $leaveDays = $leaveDays ? $leaveDays / 8 : 0;
        }

        $latestTenure = $leaveRequest->requester->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name'])
            ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        $recommendApprovers = $this->users->permissionBasedUsers('approve-recommended-leave-request');

        return view('LeaveRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withExecutiveDirectorflag($executiveDirectorflag)
            ->withLeaveDays($leaveDays)
            ->withLeaveRequest($leaveRequest)
            ->withSupervisors($supervisors)
            ->withRecommendApprovers($recommendApprovers);
    }

    public function store(StoreRequest $request, $leaveRequestId)
    {
        $inputs = $request->validated();
        $leaveRequest = $this->leaveRequests->find($leaveRequestId);
        $this->authorize('approve', $leaveRequest);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $leaveRequest = $this->leaveRequests->approve($leaveRequest->id, $inputs);

        if ($leaveRequest) {
            $message = '';
            if ($leaveRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Leave request is successfully returned.';
                $leaveRequest->requester->notify(new LeaveRequestReturned($leaveRequest));
            } else if ($leaveRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Leave request is successfully rejected.';
                $leaveRequest->requester->notify(new LeaveRequestRejected($leaveRequest));
            } else if ($leaveRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Leave request is successfully recommended.';
                $leaveRequest->approver->notify(new LeaveRequestSubmitted($leaveRequest));
            } else {
                $message = 'Leave request is successfully approved.';
                $leaveRequest->requester->notify(new LeaveRequestApproved($leaveRequest));
            }

            return redirect()->route('approve.leave.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Leave request can not be approved.');
    }
}
