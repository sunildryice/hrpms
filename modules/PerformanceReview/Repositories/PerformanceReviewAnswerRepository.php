<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewAnswer;

class PerformanceReviewAnswerRepository extends Repository
{
    public function __construct(PerformanceReviewAnswer $performanceReviewAnswer)
    {
        $this->model = $performanceReviewAnswer;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['created_by'] = auth()->user()->id;
            $performanceReviewAnswer = $this->model->create($inputs);
            DB::commit();
            return $performanceReviewAnswer;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['updated_by'] = auth()->user()->id;
            $performanceReviewAnswer = $this->model->findOrFail($id);
            $performanceReviewAnswer->fill($inputs)->save();
            DB::commit();
            return $performanceReviewAnswer;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $performanceReviewAnswer = $this->model->findOrFail($id);
            $performanceReviewAnswer->delete();
            DB::commit();
            return $performanceReviewAnswer;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}