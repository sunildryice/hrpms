<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Notifications\LeaveRequestSubmitted;
use Modules\LeaveRequest\Notifications\LeaveRequestSubmittedReview;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LeaveRequest\Requests\StoreRequest;
use Modules\LeaveRequest\Requests\UpdateRequest;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveModeRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;

class LeaveRequestController extends Controller
{
    protected $destinationPath;

    protected $sortOrder;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected LeaveModeRepository $leaveModes,
        protected LeaveRepository $employeeLeaves,
        protected LeaveRequestRepository $leaveRequests,
        protected LeaveTypeRepository $leaveTypes,
        protected OfficeRepository $offices,
        protected UserRepository $users
    ) {
        $this->destinationPath = 'leaverequest';
        $this->sortOrder = array_flip([6, 3, 12, 9]);
    }

    /**
     * Display a listing of the leave requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $employeeLeaveBalances = $this->employeeLeaves->getEmployeeLeaves($authUser->employee_id);
        $employeeLeaveBalances = $employeeLeaveBalances->filter(function ($leave) {
            return in_array($leave->leave_type_id, [config('constant.SICK_LEAVE'), config('constant.ANNUAL_LEAVE')]);
        });

        if ($request->ajax()) {
            $data = $this->leaveRequests->with(['department', 'office', 'leaveType', 'fiscalYear', 'status', 'logs', 'requester', 'childLeaveRequest', 'leaveDays'])
                ->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('start_date', 'desc')
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
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('leave.requests.detail', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                    title="View Leave Request"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('leave.requests.edit', $row->id) . '"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Edit Leave Request"><i class="bi-pencil-square"></i></a>';
                    } else
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" ';
                        $btn .= 'href="' . route('leave.requests.print', $row->id) . '">';
                        $btn .= '<i class="bi-printer"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('leave.requests.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($authUser->can('amend', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Amend Leave Request" class="btn btn-danger btn-sm amend-leave-request"';
                        $btn .= 'data-href = "' . route('leave.requests.amend.store', $row->id) . '" >';
                        $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    }

                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('LeaveRequest::index', [
            'employeeLeaveBalances' => $employeeLeaveBalances,
        ]);
    }

    /**
     * Show the form for creating a new travel request by employee.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $sql = 'SELECT u1.* FROM employee_leaves u1
            WHERE u1.employee_id=? AND u1.reported_date = (SELECT MAX(u2.reported_date)
                                                           FROM employee_leaves u2 WHERE u2.employee_id=? AND u2.leave_type_id = u1.leave_type_id )';

        $leaveIds = \DB::select($sql, [$authUser->employee_id, $authUser->employee_id]);


        $employee = $authUser->employee;
        $leaveTypes = $this->employeeLeaves->with(['leaveType'])
            ->whereIn('id', array_column($leaveIds, 'id'))
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('leaveType', function ($q) use ($employee) {
                $q->when($employee->employee_type_id == config('constant.FULL_TIME_CONSULTANT'), function ($q) {
                    $q->where('leave_frequency', 2);
                });
                $q->whereNotNull('activated_at');
            })
            ->get();


        $activeStaffs = $this->employees->getActiveEmployees();
        $substitutes = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });
        $leaveModes = $this->leaveModes
            ->where('title', '!=', 'No Leave')
            ->get();


        $leaveTypes = $leaveTypes->sortBy(function ($leave, $key) {
            return $this->sortOrder[$leave->leave_type_id] ?? PHP_INT_MAX;
        })->values();

        $supervisors = $this->users->getSupervisors($authUser);
        $holidays = $this->offices->getHolidays($authUser->employee->office_id);

        return view('LeaveRequest::create')
            ->withHolidays($holidays)
            ->withLeaveModes($leaveModes)
            ->withLeaveTypes($leaveTypes)
            ->withSubstitutes($substitutes)
            ->withSupervisors($supervisors);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $leave = $this->employeeLeaves->find($request->leave_type_id);
        $checkLeaveExists = $this->leaveRequests->checkOverlapLeaveDays(array_combine($inputs['leave_days'], $inputs['leave_mode_id']), $authUser->employee->id);

        if ($checkLeaveExists) {
            return redirect()->back()
                ->withInput()
                ->withWarningMessage('There are overlapping leave days on requested date range.');
        }

        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['office_id'] = $authUser->employee->office_id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['updated_by'] = auth()->id();

        $leaveRequest = $this->leaveRequests->create($inputs);


        if ($leaveRequest) {
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath . '/' . $leaveRequest->id, time() . '_attachment.' . $request->file('attachment')->getClientOriginalExtension());
                $leaveRequest->update(['attachment' => $filename]);
            }

            $message = 'Leave request is successfully added.';
            if ($leaveRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Leave request is successfully submitted.';
                $reviewsers = $this->users->permissionBasedUsers('review-leave-request');
                foreach ($reviewsers as $reviewer) {
                    $reviewer->notify(new LeaveRequestSubmittedReview($leaveRequest));
                }
            }

            if ($leaveRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Leave request is successfully submitted.';
                $leaveRequest->approver->notify(new LeaveRequestSubmitted($leaveRequest));
            }




            return redirect()->route('leave.requests.index')
                ->withSuccessMessage($message);
        }




        return redirect()->back()->withInput()
            ->withWarningMessage('Leave Request can not be added.');
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $leaveRequest = $this->leaveRequests->find($id);
        $this->authorize('update', $leaveRequest);

        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $sql = 'SELECT u1.* FROM employee_leaves u1
            WHERE u1.employee_id=? AND u1.reported_date = (SELECT MAX(u2.reported_date)
                                                           FROM employee_leaves u2 WHERE u2.employee_id=? AND u2.leave_type_id = u1.leave_type_id )';
        $leaveIds = \DB::select($sql, [$authUser->employee_id, $authUser->employee_id]);
        $employee = $authUser->employee;
        $leaveTypes = $this->employeeLeaves->with(['leaveType'])
            ->whereIn('id', array_column($leaveIds, 'id'))
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('leaveType', function ($q) use ($employee) {
                $q->when($employee->employee_type_id == config('constant.FULL_TIME_CONSULTANT'), function ($q) {
                    $q->where('leave_frequency', 2);
                });
                $q->whereNotNull('activated_at');
            })
            ->get();
        $leaveTypes = $leaveTypes->sortBy(function ($leave, $key) {
            return $this->sortOrder[$leave->leave_type_id] ?? PHP_INT_MAX;
        })->values();

        $activeStaffs = $this->employees->getActiveEmployees();
        $substitutes = $activeStaffs->reject(function ($staff, $key) use ($authUser) {
            return $staff->id == $authUser->employee_id;
        });

        if (
            $leaveRequest->leave_type_id == config('constant.SICK_LEAVE') ||
            $leaveRequest->leave_type_id == config('constant.ANNUAL_LEAVE')
        ) {
            $query = $this->leaveModes->query();
        } else {
            $query = $this->leaveModes->query()
                ->whereNotIn('title', ['First Half', 'Second Half']);
        }

        if (
            is_null($leaveRequest->modification_number) &&
            is_null($leaveRequest->modification_leave_request_id)
        ) {
            $query->where('title', '!=', 'No Leave');
        }

        $leaveModes = $query->get();

        $employeeLeave = $this->employeeLeaves->select(['*'])
            ->where('employee_id', $authUser->employee_id)
            ->where('fiscal_year_id', $fiscalYear->id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->first();

        $supervisors = $this->users->getSupervisors($authUser);
        $holidays = $this->offices->getHolidays($authUser->employee->office_id);

        return view('LeaveRequest::edit')
            ->withHolidays($holidays)
            ->withEmployeeLeave($employeeLeave)
            ->withLeaveModes($leaveModes)
            ->withLeaveRequest($leaveRequest)
            ->withLeaveTypes($leaveTypes)
            ->withSubstitutes($substitutes)
            ->withSupervisors($supervisors);
    }

    /**
     * Update the specified leave request in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $leaveRequest = $this->leaveRequests->find($id);
        $this->authorize('update', $leaveRequest);
        $inputs = $request->validated();
        $checkLeaveExists = $this->leaveRequests->checkOverlapLeaveDays(array_combine($inputs['leave_days'], $inputs['leave_mode_id']), $leaveRequest->requester->employee->id);
        if ($checkLeaveExists) {
            return redirect()->back()
                ->withInput()
                ->withWarningMessage('There are overlapping leave days on requested date range.');
        }
        $leave = $this->employeeLeaves->find($request->leave_type_id);
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $leaveRequest->id, time() . '_attachment.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $leaveRequest = $this->leaveRequests->update($id, $inputs);

        if ($leaveRequest) {
            $message = 'Leave request is successfully updated.';
            if ($leaveRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Leave request is successfully submitted.';
                $reviewsers = $this->users->permissionBasedUsers('review-leave-request');
                foreach ($reviewsers as $reviewer) {
                    $reviewer->notify(new LeaveRequestSubmittedReview($leaveRequest));
                }
            }
            if ($leaveRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Leave request is successfully submitted.';
                $leaveRequest->approver->notify(new LeaveRequestSubmitted($leaveRequest));
            }

            return redirect()->route('leave.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()
            ->withWarningMessage('Leave Request can not be updated.');
    }

    /**
     * Remove the specified leave request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $leaveRequest = $this->leaveRequests->find($id);
        $this->authorize('delete', $leaveRequest);
        $flag = $this->leaveRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Leave request is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Leave request can not deleted.',
        ], 422);
    }

    /**
     * Show the specified leave request.
     *
     * @return mixed
     */
    public function detail($leaveRequestId)
    {
        $authUser = auth()->user();
        $leaveRequest = $this->leaveRequests->find($leaveRequestId);

        return view('LeaveRequest::detail')
            ->withLeaveRequest($leaveRequest);
    }

    /**
     * Print the specified leave request.
     *
     * @return mixed
     */
    public function printLeave($leaveRequestId)
    {
        $authUser = auth()->user();
        $leaveRequest = $this->leaveRequests->find($leaveRequestId);
        $this->authorize('print', $leaveRequest);

        return view('LeaveRequest::print')
            ->withLeaveRequest($leaveRequest);
    }

    /**
     * Amend the specified leave request from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function amend($id)
    {
        $leaveRequest = $this->leaveRequests->find($id);
        $this->authorize('amend', $leaveRequest);
        $inputs = [
            'created_by' => auth()->id(),
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $flag = $this->leaveRequests->amend($id, $inputs);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Leave request is successfully amended.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Leave request can not amended.',
        ], 422);
    }
}
