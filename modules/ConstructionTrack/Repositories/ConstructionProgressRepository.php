<?php

namespace Modules\ConstructionTrack\Repositories;

use App\Repositories\Repository;
use Modules\ConstructionTrack\Models\ConstructionProgress;

use DB;

class ConstructionProgressRepository extends Repository
{
    public function __construct(
        ConstructionProgress $constructionProgress
    ){
        $this->model = $constructionProgress;
    }



    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $constructionProgress = $this->model->create($inputs);
             $forwardInputs = [
                    'construction_progress_id' => $constructionProgress->id,
                    'user_id' => $inputs['created_by'],
                    'status_id' => 1,
                    'log_remarks' => 'Construction Progress is created.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
            $constructionProgress->logs()->create($forwardInputs);
            DB::commit();
            return $constructionProgress;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $constructionProgress = $this->model->findOrFail($id);
            // $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            // $inputs['reviewer_id'] = $constructionProgress->approver_id;
            $constructionProgress->update($inputs);
            $constructionProgress->logs()->create($inputs);
            DB::commit();
            return $constructionProgress;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $constructionProgress = $this->model->find($id);
            $constructionProgress->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Construction Progress is updated.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                
                $constructionProgress = $this->forward($constructionProgress->id, $forwardInputs);
            }
            DB::commit();
            return $constructionProgress;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

      public function destroy($id)
    {
        try {
            $constructionProgress = $this->model->findOrFail($id);
            $constructionProgress->logs()->delete();
            $constructionProgress->attachments()->delete();
            $constructionProgress->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }
}