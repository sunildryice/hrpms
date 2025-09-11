<?php

namespace Modules\TrainingRequest\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\FiscalYear;
use Modules\TrainingRequest\Models\TrainingRequest;
use DB;

class TrainingRequestRepository extends Repository
{
    public function __construct(
        FiscalYear $fiscalYears,
        TrainingRequest $trainingRequest
    )
    {
        $this->fiscalYears = $fiscalYears;
        $this->model = $trainingRequest;
    }

    public function getTrainingNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'training_number'])
                ->where('fiscal_year_id', $fiscalYear->id)
                ->max('training_number') + 1;
        return $max;
    }

    public function approve($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->findOrFail($id);
            $trainingRequest->update($inputs);
            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                $log_remarks = 'Training request is approved.';
            } elseif ($inputs['status_id'] == config('constant.RETURNED_STATUS')) {
                $log_remarks = $inputs['log_remarks'] ?? 'Training request is returned.';
            } else {
                $log_remarks = 'Training request is rejected.';
            }
            $forwardInputs = [
                'status_id' => $inputs['status_id'],
                'log_remarks' => $log_remarks,
                'user_id' => $inputs['updated_by'],
                'original_user_id' => $inputs['original_user_id'],
            ];
            $trainingRequest->logs()->create($forwardInputs);
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function addDetails($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->find($id);
            $trainingRequest->fill($inputs)->save();

            $trainingRequest->trainingRequestQuestion()->delete();
            if (array_key_exists('textarea', $inputs)) {
                foreach ($inputs['textarea'] as $key => $value) {
                    $trainingRequest->trainingRequestQuestion()->create([
                        'training_id' => $id,
                        'question_id' => $key,
                        'answer' => $value,
                    ]);
                }
            }
            if (array_key_exists('boolean', $inputs)) {
                foreach ($inputs['boolean'] as $key => $value) {
                    $trainingRequest->trainingRequestQuestion()->create([
                        'training_id' => $id,
                        'question_id' => $key,
                        'answer' => $value,
                    ]);
                }
            }

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'status_id' => 3,
                    'log_remarks' => 'Training request details is submitted.',
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $trainingRequest = $this->forward($trainingRequest->id, $forwardInputs);
            }
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function addResponse($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->find($id);
            $trainingRequest->fill($inputs)->save();

            if (array_key_exists('textarea', $inputs)) {
                foreach ($inputs['textarea'] as $key => $value) {
                    $trainingRequest->trainingRequestQuestion()->where('question_id', $key)->delete();
                    $trainingRequest->trainingRequestQuestion()->create([
                        'training_id' => $id,
                        'question_id' => $key,
                        'answer' => $value,
                    ]);
                }
            }

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'status_id' => $inputs['status_id'],
                    'log_remarks' => 'Training response is added.',
                    'user_id' => $inputs['updated_by'],
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $trainingRequest = $this->forwardResponse($trainingRequest->id, $forwardInputs);
            }
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->findOrFail($id);

            if(!$trainingRequest->training_number){
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();
                $inputs['prefix'] = 'TR';
                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['training_number'] = $this->getTrainingNumber($fiscalYear);
            }
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $trainingRequest->update($inputs);
            $trainingRequest->logs()->create($inputs);
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forwardResponse($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->findOrFail($id);
            $trainingRequest->update($inputs);
            $trainingRequest->logs()->create($inputs);
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function recommend($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->findOrFail($id);
            $trainingRequest->update($inputs);
             $forwardInputs = [
                'status_id' => $inputs['status_id'],
                'user_id' => $inputs['updated_by'],
                'original_user_id' => $inputs['original_user_id'],
            ];
            if($inputs['status_id'] == config('constant.RETURNED_STATUS')){
                $forwardInputs['log_remarks'] = 'Training request is returned.';
            }else{
                $forwardInputs['log_remarks'] = 'Training request is recommended.';
            }
            $trainingRequest->logs()->create($forwardInputs);
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($inputs, $id)
    {
        DB::beginTransaction();
        try {
            $trainingRequest = $this->model->find($id);
            $trainingRequest->fill($inputs)->save();
            DB::commit();
            return $trainingRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
