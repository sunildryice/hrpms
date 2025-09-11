<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use Modules\EmployeeExit\Models\ExitInterviewAnswer;

use DB;

class ExitInterviewAnswerRepository extends Repository
{
    public function __construct(
        ExitInterviewAnswer $exitInterviewAnswer
    ){
        $this->model = $exitInterviewAnswer;
    }



     public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $exitInterviewAnswer = $this->model->create($inputs);
            DB::commit();
            return $exitInterviewAnswer;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

     public function updateOrCreate($inputs, $attributes=[])
    {
       DB::beginTransaction();
       try {
                $exitInterviewAnswer = $this->model->where('exit_interview_id',$inputs['exit_interview_id'])->where('question_id', $inputs['question_id'])->first();
                if($exitInterviewAnswer) {
                    $exitInterviewAnswer = $exitInterviewAnswer->update($inputs);
                } else {
                    $exitInterviewAnswer = $this->model->create($inputs);
                }
            DB::commit();
            return $exitInterviewAnswer;

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }




}
