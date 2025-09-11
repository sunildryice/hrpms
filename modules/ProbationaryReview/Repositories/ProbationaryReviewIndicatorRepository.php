<?php
namespace Modules\ProbationaryReview\Repositories;

use App\Repositories\Repository;
use Modules\ProbationaryReview\Models\ProbationaryReviewIndicator;

class ProbationaryReviewIndicatorRepository extends Repository
{
    public function __construct(ProbationaryReviewIndicator $probationaryReviewIndicator)
    {
        $this->model = $probationaryReviewIndicator;
    }
}
