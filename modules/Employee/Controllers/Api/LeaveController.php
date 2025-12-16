<?php

namespace Modules\Employee\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestDayRepository;
use Modules\Master\Repositories\FiscalYearRepository;

class LeaveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param LeaveRepository $leaves
     * @param LeaveRequestDayRepository $leaveRequestDays
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        LeaveRepository $leaves,
        LeaveRequestDayRepository $leaveRequestDays,
        FiscalYearRepository $fiscalYear,
    ) {
        $this->employees = $employees;
        $this->leaves = $leaves;
        $this->leaveRequestDays = $leaveRequestDays;
        $this->fiscalYears = $fiscalYear;
    }

    /**
     * Show the specified leave of an employee.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $employeeId, $id)
    {
        $leave = $this->leaves->find($id);
        $employee = $this->employees->find($employeeId);

        return response()->json([
            'employee' => $employee,
            'leave' => $leave,
            'leaveType' => $leave->leaveType,
            'leaveBasis' => $leave->leaveType->getLeaveBasis(),
        ]);
    }

    public function fetchLeave(Request $request, $employeeId)
    {
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $sql = "SELECT u1.* FROM employee_leaves u1
            WHERE u1.employee_id=? AND u1.reported_date = (SELECT MAX(u2.reported_date)
                                                           FROM employee_leaves u2 WHERE u2.employee_id=? AND u2.leave_type_id = u1.leave_type_id )";
        $leaveIds = \DB::select($sql, [$employeeId, $employeeId]);
        $leaveTypes = $this->leaves->with(['leaveType'])
            ->whereIn('id', array_column($leaveIds, 'id'))
            ->where('fiscal_year_id', $fiscalYear->id)
            ->whereHas('leaveType', function ($q) {
                $q->whereNotNull('activated_at');
                $q->where('encashment', 1);
            })->get();

        return response()->json([
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function checkLeave(Request $request, $employeeId)
    {
        $leaveDays = collect();
        if ($request->start_date && $request->end_date) {
            $dates = [];
            $current = strtotime($request->start_date);
            $last = strtotime($request->end_date);
            while ($current <= $last) {
                $dates[] = date('Y-m-d', $current);
                $current = strtotime('+1 day', $current);
            }
            $employee = $this->employees->find($employeeId);
            $leaveDays = $this->leaveRequestDays->select(['*'])
                ->whereHas('leaveRequest', function ($q) use ($employee) {
                    $q->where('requester_id', $employee->user->id);
                    $q->whereIn('status_id', [3, 4, 5, 6]);
                })->whereIn('leave_date', $dates)
                ->where('leave_duration', '<>', 0)->get();
        }
        return response()->json([
            'leaveDaysCount' => $leaveDays->count(),
            'message' => 'There are overlapping leave days on requested date range.',
        ]);
    }
}
