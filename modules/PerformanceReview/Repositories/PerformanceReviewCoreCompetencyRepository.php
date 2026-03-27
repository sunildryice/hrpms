<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\PerformanceReview\Models\PerformanceReviewCoreCompetency;

class PerformanceReviewCoreCompetencyRepository extends Repository
{
    public function __construct(PerformanceReviewCoreCompetency $model)
    {
        $this->model = $model;
    }
}