<?php

namespace Modules\PerformanceReview\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\PerformanceReview\Repositories\PerformanceReviewKeyGoalRepository;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;

class PerformanceReviewKeyGoalController extends Controller
{
    public function __construct(
        protected PerformanceReviewRepository $performanceReview,
        protected PerformanceReviewKeyGoalRepository $performanceReviewKeyGoal
    ) {
    }

    public function getKeyGoals($performanceId)
    {
        $performanceReview = $this->performanceReview->find($performanceId);

        return response()->json([
            'type' => 'success',
            'keyGoals' => $performanceReview->keyGoals()->where('type', 'current')->get()
        ], 200);
    }
}
