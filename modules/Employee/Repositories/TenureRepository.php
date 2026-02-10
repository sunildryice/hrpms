<?php

namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Tenure;

use DB;

class TenureRepository extends Repository
{

    public function __construct(
        Tenure $tenure,
        protected EmployeeRepository $employee
    ) {
        $this->model = $tenure;
        $this->employee = $employee;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $tenure = $this->model->create($inputs);
            $tenure->employee->update([
                'department_id' => $tenure->department_id,
                'designation_id' => $tenure->designation_id,
                'office_id' => $tenure->office_id,
                'supervisor_id' => $tenure->supervisor_id,
                'cross_supervisor_id' => $tenure->cross_supervisor_id,
                'next_line_manager_id' => $tenure->next_line_manager_id,
            ]);

            $this->updateMinimumEmployeeJoiningDate($tenure->employee_id);

            DB::commit();
            return $tenure;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $tenure = $this->model->findOrFail($id);
            $tenure->fill($inputs)->save();
            if ($tenure->id == $tenure->employee->latestTenure->id) {
                $tenure->employee->update([
                    'department_id' => $tenure->department_id,
                    'designation_id' => $tenure->designation_id,
                    'office_id' => $tenure->office_id,
                    'supervisor_id' => $tenure->supervisor_id,
                    'cross_supervisor_id' => $tenure->cross_supervisor_id,
                    'next_line_manager_id' => $tenure->next_line_manager_id,
                ]);

            }
            DB::commit();
            return $tenure;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateMinimumEmployeeJoiningDate($employeeId)
    {
        $employeeTenures = $this->model->where('employee_id', $employeeId);
        $minDate = $employeeTenures->min('joined_date');
        $employee = $this->employee->find($employeeId);
        $employee->joined_date = $minDate;
        $employee->save();
    }
}
