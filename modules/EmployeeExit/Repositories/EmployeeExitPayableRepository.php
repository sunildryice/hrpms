<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\EmployeeExitPayable;

use DB;

class EmployeeExitPayableRepository extends Repository
{
    public function __construct(
        EmployeeExitPayable $employeeExitPayable
    ){
        $this->model = $employeeExitPayable;
    }



      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $employeeExitPayable = $this->model->create($inputs);
            DB::commit();
            return $employeeExitPayable;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }


         public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeExitPayable = $this->model->findOrFail($id);
            $inputs['created_by'] = $inputs['user_id'];
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $inputs['reviewer_id'] = $employeeExitPayable->approver_id;
            $employeeExitPayable->update($inputs);
            $employeeExitPayable->logs()->create($inputs);
            DB::commit();
            return $employeeExitPayable;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeExitPayable = $this->model->find($id);
            $employeeExitPayable->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Employee Payable is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];

                $employeeExitPayable = $this->forward($employeeExitPayable->id, $forwardInputs);
            }
            DB::commit();
            return $employeeExitPayable;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $employeeExitPayable = $this->model->find($id);
            if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $employeeExitPayable->update($inputs);
            $employeeExitPayable->logs()->create($inputs);
            DB::commit();
            return $employeeExitPayable;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



}
