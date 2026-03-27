<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewCoreCompetency;

class PerformanceReviewCoreCompetencyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|exists:performance_reviews,id',
            'competencies' => 'nullable|array',
            'competencies.*.competency' => 'required|string',
            'competencies.*.rating'     => 'nullable|integer|between:1,5',
            'competencies.*.example'    => 'nullable|string',
            'competencies.*.id'         => 'nullable|integer|exists:performance_review_core_competencies,id',
        ]);

        DB::beginTransaction();

        try {
            $reviewId = $request->performance_review_id;

            // Get existing IDs
            $existingIds = PerformanceReviewCoreCompetency::where('performance_review_id', $reviewId)
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            if (!empty($request->competencies)) {
                foreach ($request->competencies as $item) {
                    if (empty(trim($item['competency'] ?? ''))) {
                        continue;
                    }

                    if (!empty($item['id'])) {
                        // UPDATE
                        $competency = PerformanceReviewCoreCompetency::find($item['id']);
                        if ($competency) {
                            $competency->update([
                                'competency' => trim($item['competency']),
                                'rating'     => $item['rating'],
                                'example'    => trim($item['example'] ?? ''),
                                'updated_by' => auth()->id(),
                            ]);
                            $submittedIds[] = $competency->id;
                        }
                    } else {
                        // CREATE
                        $new = PerformanceReviewCoreCompetency::create([
                            'performance_review_id' => $reviewId,
                            'competency'            => trim($item['competency']),
                            'rating'                => $item['rating'],
                            'example'               => trim($item['example'] ?? ''),
                            'created_by'            => auth()->id(),
                            'updated_by'            => auth()->id(),
                        ]);
                        $submittedIds[] = $new->id;
                    }
                }
            }

            // Delete rows that were removed
            $idsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($idsToDelete)) {
                PerformanceReviewCoreCompetency::whereIn('id', $idsToDelete)->delete();
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'message' => 'Core Competencies saved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to save core competencies.'
            ], 500);
        }
    }
}