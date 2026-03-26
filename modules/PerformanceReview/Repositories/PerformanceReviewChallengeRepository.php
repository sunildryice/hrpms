<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\PerformanceReview\Models\PerformanceReviewChallenge;

class PerformanceReviewChallengeRepository extends Repository
{
    public function __construct(PerformanceReviewChallenge $model)
    {
        $this->model = $model;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['created_by'] = auth()->id();
            $inputs['updated_by'] = auth()->id();

            $challenge = $this->model->create($inputs);

            DB::commit();
            return $challenge;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $challenge = $this->model->findOrFail($id);
            $inputs['updated_by'] = auth()->id();

            $challenge->update($inputs);

            DB::commit();
            return $challenge;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $challenge = $this->model->findOrFail($id);
            $challenge->delete();

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