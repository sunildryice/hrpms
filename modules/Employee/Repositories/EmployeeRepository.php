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
        return $this->model->select(['id', 'full_name', 'employee_code', 'ste_code'])
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

    public function getUpcomingBirthdays($days = 7)
    {
        $today = \Carbon\Carbon::today();
        $endDate = $today->copy()->addDays($days);

        return $this->model
            ->whereNotNull('activated_at')
            ->whereNotNull('date_of_birth')
            ->whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') BETWEEN ? AND ?", [
                $today->format('m-d'),
                $endDate->format('m-d')
            ])
            ->orderByRaw("DATE_FORMAT(date_of_birth, '%m-%d') ASC")
            ->orderBy('full_name', 'asc')
            ->get()
            ->map(function ($employee) use ($today) {
                $dobThisYear = \Carbon\Carbon::parse($employee->date_of_birth)->year($today->year);
                $daysUntil = $today->diffInDays($dobThisYear, false);

                $employee->upcoming_date = $dobThisYear;
                $employee->days_until = $daysUntil;
                $employee->label = $daysUntil === 0 ? 'Today'
                    : ($daysUntil === 1 ? 'Tomorrow' : "in {$daysUntil} days");

                return $employee;
            });
    }

    public function getUpcomingAnniversaries($days = 7)
    {
        $today = \Carbon\Carbon::today();
        $endDate = $today->copy()->addDays($days);

        return $this->model
            ->whereNotNull('activated_at')
            ->whereNotNull('joined_date')
            ->whereRaw("DATE_FORMAT(joined_date, '%m-%d') BETWEEN ? AND ?", [
                $today->format('m-d'),
                $endDate->format('m-d')
            ])
            ->orderByRaw("DATE_FORMAT(joined_date, '%m-%d') ASC")
            ->orderBy('full_name', 'asc')
            ->get()
            ->map(function ($employee) use ($today) {
                $annivThisYear = \Carbon\Carbon::parse($employee->joined_date)->year($today->year);
                $daysUntil = $today->diffInDays($annivThisYear, false);

                $employee->upcoming_date = $annivThisYear;
                $employee->days_until = $daysUntil;
                $employee->label = $daysUntil === 0 ? 'Today'
                    : ($daysUntil === 1 ? 'Tomorrow' : "in {$daysUntil} days");
                $employee->years = $today->diffInYears(\Carbon\Carbon::parse($employee->joined_date));

                return $employee;
            });
    }

    public function getUpcomingContractEndings($days = 7)
    {
        $today = \Carbon\Carbon::today();
        $endDate = $today->copy()->addDays($days);

        $employees = $this->model
            ->whereNotNull('activated_at')
            ->whereHas('latestTenure', function ($q) use ($today, $endDate) {
                $q->whereNotNull('contract_end_date')
                    ->whereBetween('contract_end_date', [$today, $endDate]);
            })
            ->with('latestTenure')
            ->orderBy('full_name')
            ->get();

        return $employees->map(function ($employee) use ($today) {
            $contractEnd = $employee->latestTenure->contract_end_date;

            $daysUntil = $today->diffInDays($contractEnd, false);

            $employee->upcoming_date = $contractEnd;
            $employee->days_until = $daysUntil;
            $employee->label = $daysUntil === 0 ? 'Today'
                : ($daysUntil === 1 ? 'Tomorrow' : "in {$daysUntil} days");

            return $employee;
        })->sortBy('upcoming_date')->values();
    }

    public function getUpcomingProbationCompletions($days = 7)
    {
        $today = \Carbon\Carbon::today();
        $endDate = $today->copy()->addDays($days);

        return $this->model
            ->whereNotNull('activated_at')
            ->whereNotNull('probation_complete_date')
            ->whereBetween('probation_complete_date', [$today, $endDate])
            ->orderBy('probation_complete_date')
            ->orderBy('full_name')
            ->get()
            ->map(function ($employee) use ($today) {
                $probationEnd = \Carbon\Carbon::parse($employee->probation_complete_date);

                $daysUntil = $today->diffInDays($probationEnd, false);

                $employee->upcoming_date = $probationEnd;
                $employee->days_until = $daysUntil;
                $employee->label = $daysUntil === 0 ? 'Today'
                    : ($daysUntil === 1 ? 'Tomorrow' : "in {$daysUntil} days");

                return $employee;
            })->sortBy('upcoming_date')->values();
    }
}
