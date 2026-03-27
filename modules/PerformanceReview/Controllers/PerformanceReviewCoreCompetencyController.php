<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PerformanceReview\Repositories\PerformanceReviewCoreCompetencyRepository;

class PerformanceReviewCoreCompetencyController extends Controller
{
    public function __construct(
        protected PerformanceReviewCoreCompetencyRepository $coreCompetencies
    ) {
    }

    public function store(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|exists:performance_reviews,id',
            'competencies' => 'nullable|array',
            'competencies.*.competency' => 'required|string',
            'competencies.*.rating' => 'nullable|integer|between:1,5',
            'competencies.*.example' => 'nullable|string',
            'competencies.*.id' => 'nullable|integer|exists:performance_review_core_competencies,id',
        ]);

        try {
            $reviewId = $request->performance_review_id;
            $competenciesData = $request->competencies ?? [];

            $this->coreCompetencies->sync($reviewId, $competenciesData);

            return response()->json([
                'type' => 'success',
                'message' => 'Core Competencies saved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to save core competencies.'
            ], 500);
        }
    }
}