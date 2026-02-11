<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Employee\Models\Employee;
use Modules\Master\Repositories\FiscalYearRepository;

class EmployeeRepository extends Repository
{
    public function __construct(
        Employee $employee,
        FiscalYearRepository $fiscalYears
    ) {
        $this->model = $employee;
        $this->fiscalYears = $fiscalYears;
    }

    public function getEmployeeByCode($employeeCode)
    {
        return $this->model->where('employee_code', $employeeCode)->first();
    }

    public function activeEmployees()
    {
        return $this->model->whereNotNull('activated_at')->orderBy('employee_code', 'asc')->get();
    }

    public function getActiveEmployeesQuery()
    {
        return $this->model
            ->select(['id', 'full_name', 'employee_code', 'employee_type_id', 'activated_at'])
            ->with(['user', 'latestTenure', 'latestTenure.office'])
            ->whereNotNull('activated_at')
            ->where(function ($q) {
                $q->whereIn('employee_type_id', [config('constant.FULL_TIME_EMPLOYEE')]);
                $q->orWhereNull('employee_type_id');
            })
            ->orderBy('employee_code', 'asc');
    }

    public function createUser($employee, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['full_name'] = $employee->getFullName();
            $inputs['reset_token'] = \Str::random(60);
            $user = $employee->user()->create($inputs);
            if (!empty($inputs['roles'])) {
                $user->roles()->sync($inputs['roles']);
            }
            DB::commit();

            return $user;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function getActiveEmployees()
    {
        return $this->model->select(['id', 'full_name', 'employee_code'])
            //            ->whereHas('user')
            ->with(['user'])
            ->whereNotNull('activated_at')
            ->where(function ($q) {
                $q->whereIn('employee_type_id', [config('constant.FULL_TIME_EMPLOYEE')]);
                $q->orWhereNull('employee_type_id');
            })->orderBy('full_name', 'asc')->get();
    }

    public function getAllEmployees()
    {
        return $this->model->select(['id', 'full_name', 'employee_code', 'official_email_address'])
            ->with(['user'])
            ->where(function ($q) {
                $q->whereIn('employee_type_id', [config('constant.FULL_TIME_EMPLOYEE')]);
                $q->orWhereNull('employee_type_id');
            })->orderBy('full_name', 'asc')->get();
    }

    public function getSupervisees($employee)
    {
        return $this->model->select(['id', 'full_name', 'employee_code'])
            ->whereNotNull('activated_at')
            ->where(function ($q) use ($employee) {
                $q->where('supervisor_id', $employee->id)
                    ->orWhere('cross_supervisor_id', $employee->id)
                    ->orWhere('next_line_manager_id', $employee->id);
            })->orderBy('full_name', 'asc')->get();
    }

    public function getActiveConsultants()
    {
        return $this->model->select(['id', 'full_name', 'employee_code'])
            ->with(['user'])
            ->whereNotNull('activated_at')
            ->where('employee_type_id', '<>', config('constant.FULL_TIME_EMPLOYEE'))
            ->orderBy('full_name', 'asc')->get();
    }

    public function getActiveMembers($exclude = null)
    {
        return $this->model->select(['id', 'full_name', 'employee_code'])
            ->whereNotNull('activated_at')
            ->when($exclude, function ($q) use ($exclude) {
                $q->where('id', '!=', $exclude);
            })
            ->orderBy('employee_type_id', 'desc')
            ->get();
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employee = $this->model->findOrFail($id);
            $employee->fill($inputs)->save();
            if ($employee->user) {
                $inputs = [
                    'full_name' => $employee->getFullName(),
                    'email_address' => $employee->official_email_address,
                    'activated_at' => $employee->activated_at,
                ];
                if (is_null($employee->activated_at)) {
                    $inputs['activated_at'] = null;
                }
                $employee->user->update($inputs);
            }
            DB::commit();

            return $employee;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateUser($employee, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['full_name'] = $employee->getFullName();
            $employee->user->update($inputs);
            if (!empty($inputs['roles'])) {
                $employee->user->roles()->sync($inputs['roles']);
            }
            DB::commit();

            return $employee->user;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function getLeaveRequests($employeeId)
    {
        $employee = $this->model->findOrFail($employeeId);
        $leaveRequests = $employee->user->leaveRequests()
            ->where('fiscal_year_id', $this->fiscalYears->getCurrentFiscalYearId())
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->orderBy('start_date', 'desc')
            ->get();

        return $leaveRequests;
    }

    public function getLeaveRequestsOfCurrentAndPreviousFiscalYear($employeeId)
    {
        $employee = $this->model->findOrFail($employeeId);
        $leaveRequests = $employee->user->leaveRequests()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) {
                $q->where('fiscal_year_id', $this->fiscalYears->getCurrentFiscalYearId());
                $q->orWhere('fiscal_year_id', $this->fiscalYears->where('title', '=', date('Y', strtotime('-1 year')))->first()->id);
            })
            ->orderBy('start_date', 'desc')
            ->get();

        return $leaveRequests;
    }

    public function getLeaveEncashRequestsOfCurrentAndPreviousFiscalYear($employeeId)
    {
        $employee = $this->model->findOrFail($employeeId);
        $leaveRequests = $employee->leaveEncashments()
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])
            ->where(function ($q) {
                $q->where('fiscal_year_id', $this->fiscalYears->getCurrentFiscalYearId());
                $q->orWhere('fiscal_year_id', $this->fiscalYears->where('title', '=', date('Y', strtotime('-1 year')))->first()->id);
            })
            ->orderBy('request_date', 'desc')
            ->get();

        return $leaveRequests;
    }

    // public function indexQuery(){
    //     return $this->model->select(['*'])
    //         ->join('employee_services as s1', function($join){
    //             $join->where('s1.employee_id' , '=', 'employeees.id');
    //         });
    // }
}
