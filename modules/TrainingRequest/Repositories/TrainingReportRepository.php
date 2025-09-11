<?php

namespace Modules\TrainingRequest\Repositories;

use App\Repositories\Repository;
use Modules\TrainingRequest\Models\TrainingReport;
use Modules\TrainingRequest\Models\TrainingReportQuestion;

use DB;

class TrainingReportRepository extends Repository
{
    public function __construct(TrainingReport $trainingReport, TrainingReportQuestion $trainingReportQuestion)
    {
        $this->model = $trainingReport;
        $this->trainingReportQuestion = $trainingReportQuestion;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            if($inputs['status_id']== 4){
                $inputs['log_remarks'] = 'Training report has been recommended.';
            }elseif($inputs['status_id']== 6){
                $inputs['log_remarks'] = 'Training report has been approved.';
            }else{
                $inputs['log_remarks'] = 'Training report has been rejected.';
            }
            $trainingReport = $this->model->find($id);
            $trainingReport->update($inputs);
            $trainingReport->logs()->create($inputs);
            DB::commit();
            return $trainingReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $trainingReport = $this->model->updateOrCreate(['training_id'=>$inputs['training_id']], $inputs);
            $trainingReport->trainingReportQuestion()->delete();
            foreach ($inputs['textarea'] as $key => $value) {
                $trainingReport->trainingReportQuestion()->create([
                    'training_report_id'=>$trainingReport->id,
                    'question_id' => $key,
                    'answer'=>$value,
                ]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['user_id'],
                    'log_remarks' => 'Training report is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'reviewer_id' => $inputs['reviewer_id'],
                    'status_id' => 3,
                ];
                $trainingReport = $this->forward($trainingReport->id, $forwardInputs);
            }
            DB::commit();
            return $trainingReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $trainingReport = $this->model->updateOrCreate(['training_id'=>$inputs['training_id']], $inputs);
            foreach ($inputs['textarea'] as $key => $value) {
                $trainingReportQuestion = $this->trainingReportQuestion
                                                ->where('training_report_id',$id)
                                                ->where('question_id',$key)
                                                ->first();
                $trainingReportQuestion->update(['answer'=>$value]);
                // $trainingReport->trainingReportQuestion()->create([
                //     'training_id'=>$inputs['training_id'],
                //     'question_id' => $key,
                //     'answer'=>$value,
                // ]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['user_id'],
                    'log_remarks' => 'Training report is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'reviewer_id' => $inputs['reviewer_id'],
                    'status_id' => 3,
                ];
                $trainingReport = $this->forward($trainingReport->id, $forwardInputs);
            }
            DB::commit();
            return $trainingReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $trainingReport = $this->model->findOrFail($id);
            $trainingReport->update($inputs);
            $trainingReport->logs()->create($inputs);
            DB::commit();
            return $trainingReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);
            return false;
        }
    }

}
