<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Repositories\PerformanceReviewChallengeRepository;

class PerformanceReviewChallengeController extends Controller
{
    public function __construct(
        protected PerformanceReviewChallengeRepository $challenges
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|exists:performance_reviews,id',
            'challenges'            => 'nullable|array',
            'challenges.*.challenge'=> 'nullable|string',
            'challenges.*.result'   => 'nullable|string',
            'challenges.*.id'       => 'nullable|integer|exists:performance_review_challenges,id',
        ]);

        try {
            $reviewId = $request->performance_review_id;
            $challengesData = $request->challenges ?? [];

            $this->challenges->sync($reviewId, $challengesData);

            return response()->json([
                'type'    => 'success',
                'message' => 'Challenges synced successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'type'    => 'error',
                'message' => 'Failed to save challenges.'
            ], 500);
        }
    }
}