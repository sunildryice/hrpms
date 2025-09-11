<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitInterviewRatingAnswer;

use DB;

class ExitInterviewRatingAnswerRepository extends Repository
{
    public function __construct(
        ExitInterviewRatingAnswer $exitInterviewRatingAnswer
    ){
        $this->model = $exitInterviewRatingAnswer;
    }



     public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterviewRatingAnswer = $this->model->create($inputs);
            DB::commit();
            return $exitInterviewRatingAnswer;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateOrCreate($inputs, $attributes=[])
    {
        DB::beginTransaction();
       try {
                $exitInterviewRatingAnswer = $this->model->where('exit_interview_id',$inputs['exit_interview_id'])->where('exit_rating_id', $inputs['exit_rating_id'])->first();
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
