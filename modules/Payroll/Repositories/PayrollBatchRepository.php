<?php

namespace Modules\Payroll\Repositories;

use App\Repositories\Repository;
use Modules\Payroll\Models\PayrollBatch;

use DB;

class PayrollBatchRepository extends Repository
{
    public function __construct(PayrollBatch $payrollBatch)
    {
        $this->model = $payrollBatch;
    }

    public function getApproved()
    {
        return $this->model
        ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
        ->orderBy('created_at', 'desc')->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $payrollBatch = $this->model->find($id);
            $payrollBatch->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $payrollBatch->logs()->create($inputs);
            DB::commit();
            return $payrollBatch;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $payrollBatch = $this->model->findOrFail($id);
            $payrollBatch->sheetDetails()->delete();
            $payrollBatch->sheets()->delete();
            $payrollBatch->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $payrollBatch = $this->model->findOrFail($id);
            $payrollBatch->update($inputs);
            $payrollBatch->logs()->create($inputs);
            DB::commit();
            return $payrollBatch;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $payrollBatch = $this->model->find($id);
            $payrollBatch->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $payrollBatch->logs()->create($inputs);
            DB::commit();
            return $payrollBatch;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $payrollBatch = $this->model->find($id);
            $payrollBatch->fill($inputs)->save();
            if ($inputs['status_id'] == 3) {
                $forwardInputs = [
                    'status_id' => 3,
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Payroll batch is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $this->forward($payrollBatch->id, $forwardInputs);
            }

            DB::commit();
            return $payrollBatch;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
