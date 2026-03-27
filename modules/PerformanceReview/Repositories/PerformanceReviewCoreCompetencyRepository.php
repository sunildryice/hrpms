<?php

namespace Modules\PerformanceReview\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceReviewCoreCompetency;

class PerformanceReviewCoreCompetencyRepository extends Repository
{
    public function __construct(PerformanceReviewCoreCompetency $model)
    {
        $this->model = $model;
    }

    /**
     * Sync core competencies (Create / Update / Delete)
     */
    public function sync($reviewId, array $competencies = [])
    {
        DB::beginTransaction();

        try {
            // Get existing IDs
            $existingIds = $this->model
                ->where('performance_review_id', $reviewId)
                ->pluck('id')
                ->toArray();

            $submittedIds = [];

            foreach ($competencies as $item) {
                if (empty(trim($item['competency'] ?? ''))) {
                    continue;
                }

                if (!empty($item['id'])) {
                    // Update existing competency
                    $competency = $this->model->find($item['id']);
                    if ($competency) {
                        $competency->update([
                            'competency' => trim($item['competency']),
                            'rating' => $item['rating'],
                            'example' => trim($item['example'] ?? ''),
                            'updated_by' => auth()->id(),
                        ]);
                        $submittedIds[] = $competency->id;
                    }
                } else {
                    // Create new competency
                    $new = $this->model->create([
                        'performance_review_id' => $reviewId,
                        'competency' => trim($item['competency']),
                        'rating' => $item['rating'],
                        'example' => trim($item['example'] ?? ''),
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                    $submittedIds[] = $new->id;
                }
            }

            // Delete rows
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