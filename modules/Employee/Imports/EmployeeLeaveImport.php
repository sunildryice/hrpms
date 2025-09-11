<?php

namespace Modules\Employee\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\Leave;
use Modules\Master\Models\LeaveType;

class EmployeeLeaveImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $sickLeave = LeaveType::where('title', 'Sick Leave')->first();
        $annualLeave = LeaveType::where('title', 'Annual Leave')->first();

        if ($row['employee_name'] && $row['employee_code'] && $row['sick_leave'] && $row['annual_leave']) {
            $employee = Employee::where('employee_code', $row['employee_code'])->first();
            if($employee){
                $sickLeaves = $employee->leaves->filter(function ($leave) use ($sickLeave){
                    return $leave->leave_type_id == $sickLeave->id;
                })->first();
                $annualLeaves = $employee->leaves->filter(function ($leave) use ($annualLeave){
                    return $leave->leave_type_id == $annualLeave->id;
                })->first();

                if(!$sickLeaves){
                    $employeeLeave = Leave::create([
                        'employee_id'=>$employee->id,
                        'fiscal_year_id'=>1,
                        'leave_type_id'=>$sickLeave->id,
                        'reported_date'=>date('2022-09-01'),
                        'opening_balance'=>$row['sick_leave'],
                        'earned'=>0,
                        'taken'=>0,
                        'paid'=>0,
                        'lapsed'=>0,
                        'balance'=>$row['sick_leave'],
                        'remarks'=>'Opening leave'
                    ]);
                    $employeeLeave->logs()->create([
                        'fiscal_year_id' => $employeeLeave->fiscal_year_id,
                        'month' => date('m', strtotime($employeeLeave->reported_date)),
                    ]);
                }
                if(!$annualLeaves){
                    $employeeLeave = Leave::create([
                        'employee_id'=>$employee->id,
                        'fiscal_year_id'=>1,
                        'leave_type_id'=>$annualLeave->id,
                        'reported_date'=>date('2022-09-01'),
                        'opening_balance'=>$row['annual_leave'],
                        'earned'=>0,
                        'taken'=>0,
                        'paid'=>0,
                        'lapsed'=>0,
                        'balance'=>$row['annual_leave'],
                        'remarks'=>'Opening leave'
                    ]);
                    $employeeLeave->logs()->create([
                        'fiscal_year_id' => $employeeLeave->fiscal_year_id,
                        'month' => date('m', strtotime($employeeLeave->reported_date)),
                    ]);
                }
            }
        }
    }
}
