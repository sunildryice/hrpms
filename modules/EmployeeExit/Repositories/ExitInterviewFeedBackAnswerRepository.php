<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitInterviewFeedbackAnswer;

use DB;

class ExitInterviewFeedBackAnswerRepository extends Repository
{
    public function __construct(
        ExitInterviewFeedbackAnswer $exitInterviewFeedback
    ){
        $this->model = $exitInterviewFeedback;
    }



     public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterviewFeedback = $this->model->create($inputs);
            DB::commit();
            return $exitInterviewFeedback;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

     public function updateOrCreate($inputs, $attributes=[])
    {
        DB::beginTransaction();
       try {
                $exitInterviewRatingAnswer = $this->model->where('exit_interview_id',$inputs['exit_interview_id'])->where('exit_feedback_id', $inputs['exit_feedback_id'])->first();
                // $exitInterviewRatingAnswer = $this->model->updateOrCreate(['exit_interview_id'=>$inputs['exit_interview_id'],'exit_feedback_id', $inputs['exit_feedback_id']], $inputs);
                if($exitInterviewRatingAnswer) {
                    $exitInterviewRatingAnswer = $exitInterviewRatingAnswer->update($inputs);
                } else {
                    $exitInterviewRatingAnswer = $this->model->create($inputs);
                }
            DB::commit();
            return $exitInterviewRatingAnswer;

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



}
