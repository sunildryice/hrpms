<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\PerformanceReview\Models\PerformanceProfessionalDevelopmentPlan;

class PerformanceProfessionalDevelopmentPlanRepository extends Repository
{
    public function __construct(PerformanceProfessionalDevelopmentPlan $model)
    {
        $this->model = $model;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['created_by'] = auth()->id();

            $plan = $this->model->create($inputs);

            DB::commit();
            return $plan;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $plan = $this->model->findOrFail($id);

            $inputs['updated_by'] = auth()->id();

            $plan->update($inputs);

            DB::commit();
            return $plan;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $plan = $this->model->findOrFail($id);
            $plan->delete();

            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getByReview($performanceReviewId)
    {
        return $this->model
            ->where('performance_review_id', $performanceReviewId)
            ->get();
    }
}