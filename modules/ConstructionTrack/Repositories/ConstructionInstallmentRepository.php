<?php

namespace Modules\ConstructionTrack\Repositories;

use App\Repositories\Repository;
use Modules\ConstructionTrack\Models\ConstructionInstallment;

use Illuminate\Support\Facades\DB;
use Throwable;

class ConstructionInstallmentRepository extends Repository
{
    public function __construct(
        ConstructionInstallment $constructionInstallment
    ){
        $this->model = $constructionInstallment;
    }


     public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $constructionInstallment = $this->model->create($inputs);
             $forwardInputs = [
                    'construction_progress_id' => $constructionInstallment->id,
                    'user_id' => $inputs['created_by'],
                    'status_id' => 1,
                    'log_remarks' => 'Construction Installment is created.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
            $constructionInstallment->logs()->create($forwardInputs);
            DB::commit();
            return $constructionInstallment;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $constructionInstallment = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            // $inputs['reviewer_id'] = $constructionInstallment->approver_id;
            $constructionInstallment->update($inputs);
            $constructionInstallment->logs()->create($inputs);
            DB::commit();
            return $constructionInstallment;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $constructionInstallment = $this->model->find($id);
            $constructionInstallment->fill($inputs)->save();
            // if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Construction Progress is updated.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
               
                // $constructionInstallment = $this->forward($constructionInstallment->id, $forwardInputs);
            // }
            DB::commit();
            return $constructionInstallment;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

       public function destroy($id)
    {
        try {
            $constructionInstallment = $this->model->findOrFail($id);
            $constructionInstallment->logs()->delete();
            $constructionInstallment->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    
    public function getInstallmentNumber($constructionId)
    {
        return ($this->model->where('construction_id', '=', $constructionId)->count()+1);
    }

    public function submit($installmentId, $inputs)
    {
        DB::beginTransaction();
        try {
            $installment = $this->model->findOrFail($installmentId);

            $authUser                   = auth()->user();
            $inputs['requester_id']     = $authUser->id;
            $inputs['user_id']          = $authUser->id;
            $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
            $inputs['status_id']        = config('constant.SUBMITTED_STATUS');
            $inputs['log_remarks']      = 'Construction installment submitted.';

            $installment->update($inputs);
            $installment->logs()->create($inputs);
            DB::commit();
            return $installment;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function verify($installmentId, $inputs)
    {
        DB::beginTransaction();
        try {
            $installment = $this->model->findOrFail($installmentId);
            $installment->update($inputs);
            $installment->logs()->create($inputs);
            DB::commit();
            return $installment;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function approve($installmentId, $inputs)
    {
        DB::beginTransaction();
        try {
            $installment = $this->model->findOrFail($installmentId);
            $installment->update($inputs);
            $installment->logs()->create($inputs);
            DB::commit();
            return $installment;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }    
    
}