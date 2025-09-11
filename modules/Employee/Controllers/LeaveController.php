<?php

namespace Modules\Employee\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Exports\Leave\LeaveExportController;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Employee\Requests\Leave\UpdateRequest;

class LeaveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param LeaveRepository $leaves
     * @return void
     */
    public function __construct(
        EmployeeRepository $employees,
        LeaveRepository    $leaves
    )
    {
        $this->employees = $employees;
        $this->leaves = $leaves;
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
        if ($request->expectsJson()) {
            return response()->json([
                'employee' => $employee,
                'leave' => $leave,
                'leaveType' => $leave->leaveType,
                'leaveBasis' => $leave->leaveType->getLeaveBasis(),
            ]);
        }
        return view('Employee::Leave.show')
            ->withLeave($leave);
    }

    /**
     * Show the form for editing the specified leave of an employee.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $employeeId, $id)
    {
        $leave = $this->leaves->find($id);
        return view('Employee::Leave.edit')
            ->withLeave($leave);
    }

    /**
     * Update the specified leave of an employee in storage.
     *
     * @param \Modules\Employee\Requests\Experience\UpdateRequest $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateRequest $request, $employeeId, $id)
    {
        $leave = $this->leaves->find($id);
        $inputs = $request->validated();
        $inputs['balance'] = $inputs['opening_balance'] + $inputs['earned'] - $inputs['taken'] - $inputs['lapsed'];

        if ($leave->leaveType->maximum_carry_over > 0 && $request->maximum_carryover < $inputs['balance']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Total balance can not be greater than allowed carry over.'
            ], 422);
        }
        $inputs['updated_by'] = auth()->id();
        $leave = $this->leaves->update($id, $inputs);

        if ($leave) {
            return response()->json(['status' => 'ok',
                'leave' => $leave,
                'message' => 'Employee leave is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Employee leave can not be updated.'], 422);
    }

    public function export($employeeId)
    {
        return new LeaveExportController($employeeId);
    }

    public function exportYear($employeeId, $year)
    {
        return new LeaveExportController($employeeId, $year);
    }
}
