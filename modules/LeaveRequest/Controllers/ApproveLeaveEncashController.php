<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Notifications\LeaveEncashApproved;
use Modules\LeaveRequest\Notifications\LeaveEncashRejected;
use Modules\LeaveRequest\Notifications\LeaveEncashReturned;
use Modules\LeaveRequest\Notifications\LeaveEncashSubmitted;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Requests\Approve\StoreRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveModeRepository;
use Modules\Privilege\Repositories\UserRepository;

class ApproveLeaveEncashController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LeaveRepository $employeeLeaves
     * @param LeaveEncashRepository $leaveEncash
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        LeaveRepository $employeeLeaves,
        LeaveModeRepository $leaveModes,
        LeaveEncashRepository $leaveEncash,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->employeeLeaves = $employeeLeaves;
        $this->fiscalYears = $fiscalYears;
        $this->leaveModes = $leaveModes;
        $this->leaveEncash = $leaveEncash;
        $this->users = $users;
    }

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
            $data = $this->leaveEncash->with(['department', 'office', 'leaveType', 'fiscalYear', 'status', 'requester.employee'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.VERIFIED_STATUS')])
                ->orderBy('request_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('leave_type', function ($row) {
                    return $row->getLeaveType();
                })->addColumn('request_date', function ($row) {
                return $row->getRequestDate();
            })->addColumn('encash_balance', function ($row) {
                return $row->encash_balance;
            })->addColumn('encash_number', function ($row) {
                return $row->getEncashNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('employee', function ($row) {
                return $row->getEmployeeName();
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '';
                if ($authUser->can('approve', $row)) {
                $btn = '<a href="' . route('approve.leave.encash.create', $row->id) . '"' .
                    'class="act-btns bt-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve encash request"><i class="bi bi-box-arrow-in-up-right"></i></a>';
                }
                return $btn;
            })->rawColumns(['action', 'duration'])
                ->make(true);
        }

        return view('LeaveRequest::LeaveEncash.Approve.index');
    }

    public function create($id)
    {
        $authUser = auth()->user();
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('approve', $leaveEncash);
        $latestTenure = $leaveEncash->requester->employee->latestTenure;
        $supervisors = $this->users->select(['id', 'full_name'])
            ->whereIn('employee_id', [$latestTenure->cross_supervisor_id, $latestTenure->next_line_manager_id])
            ->get();

        $recommendApprovers = $this->users->permissionBasedUsers('approve-leave-encash');

        return view('LeaveRequest::LeaveEncash.Approve.create')
            ->withAuthUser($authUser)
            ->withLeaveEncash($leaveEncash)
            ->withSupervisors($supervisors)
            ->withRecommendApprovers($recommendApprovers);
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('approve', $leaveEncash);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $leaveEncash = $this->leaveEncash->approve($leaveEncash->id, $inputs);

        if ($leaveEncash) {
            $message = '';
            if ($leaveEncash->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Leave request is successfully returned.';
                $leaveEncash->requester->notify(new LeaveEncashReturned($leaveEncash));
            } else if ($leaveEncash->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Leave request is successfully rejected.';
                $leaveEncash->requester->notify(new LeaveEncashRejected($leaveEncash));
            } else if ($leaveEncash->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Leave request is successfully recommended.';
                $leaveEncash->approver->notify(new LeaveEncashSubmitted($leaveEncash));
            } else {
                $message = 'Leave request is successfully approved.';
                $leaveEncash->requester->notify(new LeaveEncashApproved($leaveEncash));
                $leaveEncash->employee->user->notify(new LeaveEncashApproved($leaveEncash));
            }
            return redirect()->route('approve.leave.encash.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Leave request can not be approved.');
    }
}
