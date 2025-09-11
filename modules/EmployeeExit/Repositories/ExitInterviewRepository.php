<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitInterview;

use DB;

class ExitInterviewRepository extends Repository
{
    public function __construct(
        ExitInterview $exitInterview
    ){
        $this->model = $exitInterview;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterview = $this->model->find($id);
            if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $exitInterview->update($inputs);
            $exitInterview->logs()->create($inputs);
            DB::commit();
            return $exitInterview;
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
            $exitInterview = $this->model->create($inputs);
            DB::commit();
            return $exitInterview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterview = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            // $inputs['reviewer_id'] = $exitInterview->approver_id;
            $exitInterview->update($inputs);
            $exitInterview->logs()->create($inputs);
            DB::commit();
            return $exitInterview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterview = $this->model->find($id);
            $exitInterview->fill($inputs)->save();
           if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    // 'prefix'=>'AR',
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Exit Interview is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $exitInterview = $this->forward($exitInterview->id, $forwardInputs);
             }
            DB::commit();
            return $exitInterview;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



     // public function destroy($id)
     // {
     //     try {
     //         $exitInterview = $this->model->findOrFail($id);
     //         $exitInterview->logs()->delete();
     //         $exitInterview->advanceRequestDetails()->delete();
     //         $exitInterview->delete();
     //         return true;
     //     } catch (\Illuminate\Database\QueryException $e) {
     //         return false;
     //     }
     // }



}
