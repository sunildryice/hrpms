<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewKeyGoal;

class PerformanceReviewKeyGoalRepository extends Repository
{
    public function __construct(PerformanceReviewKeyGoal $performanceReviewKeyGoal)
    {
        $this->model = $performanceReviewKeyGoal;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['created_by'] = auth()->user()->id;
            $performanceReviewKeyGoal = $this->model->create($inputs);
            DB::commit();
            return $performanceReviewKeyGoal;
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
            $performanceReviewKeyGoal = $this->model->findOrFail($id);
            $performanceReviewKeyGoal->fill($inputs)->save();
            DB::commit();
            return $performanceReviewKeyGoal;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $performanceReviewKeyGoal = $this->model->findOrFail($id);
            $performanceReviewKeyGoal->delete();
            DB::commit();
            return $performanceReviewKeyGoal;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function createFromPrevious($inputs)
    {
        DB::beginTransaction();
        try {
            $performanceReviewKeyGoal = $this->model->create($inputs);
            DB::commit();
            return $performanceReviewKeyGoal;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}