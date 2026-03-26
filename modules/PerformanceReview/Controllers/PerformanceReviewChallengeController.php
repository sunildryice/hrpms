<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewChallenge;

class PerformanceReviewChallengeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|exists:performance_reviews,id',
            'challenges' => 'nullable|array',
            'challenges.*.challenge' => 'nullable|string',
            'challenges.*.result' => 'nullable|string',
            'challenges.*.id' => 'nullable|integer|exists:performance_review_challenges,id',
        ]);

        DB::beginTransaction();
        try {
            // Delete all existing challenges for this review
            PerformanceReviewChallenge::where('performance_review_id', $request->performance_review_id)
                ->delete();

            if (!empty($request->challenges)) {
                foreach ($request->challenges as $item) {
                    if (empty(trim($item['challenge'] ?? ''))) {
                        continue;
                    }

                    PerformanceReviewChallenge::create([
                        'performance_review_id' => $request->performance_review_id,
                        'challenge' => trim($item['challenge']),
                        'result' => trim($item['result'] ?? ''),
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();
            return response()->json(['type' => 'success', 'message' => 'Challenges saved successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['type' => 'error', 'message' => 'Failed to save challenges.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $challenge = PerformanceReviewChallenge::findOrFail($request->challenge_id);
        $challenge->delete();

        return response()->json(['type' => 'success', 'message' => 'Challenge deleted.']);
    }
}