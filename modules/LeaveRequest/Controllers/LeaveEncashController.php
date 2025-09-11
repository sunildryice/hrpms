<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Notifications\LeaveEncashSubmitted;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LeaveRequest\Requests\LeaveEncash\StoreRequest;
use Modules\LeaveRequest\Requests\LeaveEncash\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveModeRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;

class LeaveEncashController extends Controller
{
    private $employees;
    private $employeeLeaves;
    private $fiscalYears;
    private $leaveModes;
    private $leaveEncash;
    private $leaveTypes;
    private $offices;
    private $users;

    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LeaveModeRepository $leaveModes
     * @param LeaveRepository $employeeLeaves
     * @param LeaveRequestRepository $leaveEncash
     * @param LeaveTypeRepository $leaveTypes
     * @param OfficeRepository $offices
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        LeaveModeRepository $leaveModes,
        LeaveRepository $employeeLeaves,
        LeaveEncashRepository $leaveEncash,
        LeaveTypeRepository $leaveTypes,
        OfficeRepository $offices,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->employeeLeaves = $employeeLeaves;
        $this->fiscalYears = $fiscalYears;
        $this->leaveModes = $leaveModes;
        $this->leaveEncash = $leaveEncash;
        $this->leaveTypes = $leaveTypes;
        $this->offices = $offices;
        $this->users = $users;
    }

    /**
     * Display a listing of the leave encash requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->leaveEncash->with(['department', 'office', 'leaveType', 'fiscalYear', 'status', 'logs'])
                ->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
                $q->orWhere('original_user_id', $authUser->id);
            })->orWhere('employee_id', $authUser->employee_id)
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
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('leave.encash.show', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                    title="View Leave encash request"><i class="bi bi-eye"></i></a>';
                if ($authUser->can('update', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('leave.encash.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Leave encash request"><i class="bi-pencil-square"></i></a>';
                } else if ($authUser->can('print', $row)) {
                    $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" ';
                    $btn .= 'href="' . route('leave.encash.print', $row->id) . '">';
                    $btn .= '<i class="bi-printer"></i></a>';
                }
                if ($authUser->can('delete', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('leave.encash.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                }
                if ($authUser->can('amend', $row)) {
                    $btn .= '&emsp;<a href = "javascript:;" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Amend Leave encash request" class="btn btn-danger btn-sm amend-leave-request"';
                    $btn .= 'data-href = "' . route('leave.encash.amend', $row->id) . '" >';
                    $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                }
                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('LeaveRequest::LeaveEncash.index');
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $employees = $this->employees->getActiveEmployees();
        $reviewers = $this->users->permissionBasedUsers('review-leave-encash');
        $approvers = $this->users->permissionBasedUsers('approve-leave-encash');
        $leaveTypes = $this->leaveTypes->select(['*'])
            ->where('encashment', 1)
            ->get();
        return view('LeaveRequest::LeaveEncash.create')
            ->withEmployees($employees)
            ->withReviewers($reviewers)
            ->withLeaveTypes($leaveTypes)
            ->withApprovers($approvers);
    }

    /**
     * Store a newly created leave encashment in storage.
     *
     * @param \Modules\LeaveRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $leaveEncash = $this->leaveEncash->create($inputs);
        if ($leaveEncash) {
            $message = 'Leave Encash Request is successfully added.';
            if ($leaveEncash->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Leave encash request is successfully submitted.';
                $leaveEncash->reviewer->notify(new LeaveEncashSubmitted($leaveEncash));
            }
            return redirect()->route('leave.encash.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Leave Encashment Request can not be added.');
    }

    /**
     *
     * Show the form for editing the specified travel request.
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('update', $leaveEncash);
        $employees = $this->employees->getActiveEmployees();
        $reviewers = $this->users->permissionBasedUsers('review-leave-encash');
        $approvers = $this->users->permissionBasedUsers('approve-leave-encash');

        return view('LeaveRequest::LeaveEncash.edit')
            ->withLeaveEncash($leaveEncash)
            ->withEmployees($employees)
            ->withReviewers($reviewers)
            ->withApprovers($approvers);
    }

    /**
     * Update the specified leave encash request in storage.
     *
     * @param \Modules\LeaveRequest\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('update', $leaveEncash);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $leaveEncash = $this->leaveEncash->update($id, $inputs);
        if ($leaveEncash) {
            $message = 'Leave Encashment Request is successfully added.';
            if ($leaveEncash->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Leave encash request is successfully submitted.';
                $leaveEncash->approver->notify(new LeaveEncashSubmitted($leaveEncash));
            }
            return redirect()->route('leave.encash.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Leave Encashment Request can not be updated.');
    }

    /**
     * Remove the specified leave encash request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('delete', $leaveEncash);
        $flag = $this->leaveEncash->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Leave encash request is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Leave encash request can not deleted.',
        ], 422);
    }

    /**
     * Show the specified leave encash request.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);

        return view('LeaveRequest::LeaveEncash.show')
            ->withLeaveEncash($leaveEncash);
    }

    /**
     * Print the specified leave encash request.
     *
     * @param $leaveRequestId
     * @return mixed
     */
    public function print($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('print', $leaveEncash);

        return view('LeaveRequest::LeaveEncash.print')
            ->withLeaveEncash($leaveEncash);
    }

    /**
     * Amend the specified leave encash request from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('amend', $leaveEncash);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->leaveEncash->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Leave encash request is successfully amended.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Leave encash request can not amended.',
        ], 422);
    }
}
