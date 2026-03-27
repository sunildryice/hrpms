<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewChallenge;

class PerformanceReviewChallengeRepository extends Repository
{
    public function __construct(PerformanceReviewChallenge $model)
    {
        $this->model = $model;
    }

    /**
     * Sync challenges (create/update/delete)
     */
    public function sync($reviewId, array $challenges = [])
    {
        DB::beginTransaction();

        try {
            // Get existing IDs
            $existingIds = $this->model
                ->where('performance_review_id', $reviewId)
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            foreach ($challenges as $item) {
                if (empty(trim($item['challenge'] ?? ''))) {
                    continue;
                }

                if (!empty($item['id'])) {
                    // Update existing
                    $challenge = $this->model->find($item['id']);
                    if ($challenge) {
                        $challenge->update([
                            'challenge'  => trim($item['challenge']),
                            'result'     => trim($item['result'] ?? ''),
                            'updated_by' => auth()->id(),
                        ]);
                        $submittedIds[] = $challenge->id;
                    }
                } else {
                    // Create new
                    $new = $this->model->create([
                        'performance_review_id' => $reviewId,
                        'challenge'             => trim($item['challenge']),
                        'result'                => trim($item['result'] ?? ''),
                        'created_by'            => auth()->id(),
                        'updated_by'            => auth()->id(),
                    ]);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete removed rows
            $idsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($idsToDelete)) {
                $this->model->whereIn('id', $idsToDelete)->delete();
            }

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;   
        }
    }
}