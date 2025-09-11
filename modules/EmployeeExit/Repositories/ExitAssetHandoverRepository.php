<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitAssetHandover;

use DB;

class ExitAssetHandoverRepository extends Repository
{
    public function __construct(
        ExitAssetHandover $exitAssetHandover
    ){
        $this->model = $exitAssetHandover;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitAssetHandover = $this->model->find($id);
            if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $exitAssetHandover->update($inputs);
            $exitAssetHandover->logs()->create($inputs);
            DB::commit();
            return $exitAssetHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $exitAssetHandover = $this->model->create($inputs);
            DB::commit();
            return $exitAssetHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitAssetHandover = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $exitAssetHandover->update($inputs);
            $exitAssetHandover->logs()->create($inputs);
            DB::commit();
            return $exitAssetHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitAssetHandover = $this->model->find($id);
            $exitAssetHandover->fill($inputs)->save();
           if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Exit Interview is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $exitAssetHandover = $this->forward($exitAssetHandover->id, $forwardInputs);
             }
            DB::commit();
            return $exitAssetHandover;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
