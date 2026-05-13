<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PerformanceReview\Models\PerformanceProfessionalDevelopmentPlan;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewDevPlanController extends Controller
{
    protected PerformanceReview $performanceReview;

    public function __construct(PerformanceReview $performanceReview)
    {
        $this->performanceReview = $performanceReview;
    }

    /**
     * Show the standalone Development Plan page for the authenticated employee.
     * Looks up the employee's most recent Key Goals Review across ALL fiscal years,
     * so they can update dev plans at any time (e.g. after a training session).
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'No employee record linked to your account.');
        }

        // Find the latest Key Goals Review (review_type_id = 3) for this employee.
        // Order by fiscal year descending so the most recent one is used.
        $keyGoalReview = $this->performanceReview
            ->with(['developmentPlans', 'fiscalYear'])
            ->where('employee_id', $employee->id)
            ->where('review_type_id', 3) // Key Goals Review
            ->orderByDesc('created_at')
            ->first();

        if (!$keyGoalReview) {
            return view('PerformanceReview::DevPlan.index', [
                'keyGoalReview'  => null,
                'devPlans'       => collect(),
            ]);
        }

        return view('PerformanceReview::DevPlan.index', [
            'keyGoalReview' => $keyGoalReview,
            'devPlans'      => $keyGoalReview->developmentPlans,
        ]);
    }

    /**
     * Save (upsert) development plans for the given performance review.
     * This is intentionally kept as a separate endpoint so it can be called
     * from both the existing fill page and the new standalone dev-plan page.
     */
    public function update(Request $request)
    {
        $request->validate([
            'performance_review_id'  => 'required|integer|exists:performance_reviews,id',
            'devplans'               => 'required|array|min:1',
            'devplans.*.plan'        => 'required|string|max:2000',
        ]);

        $performanceReview = $this->performanceReview->findOrFail($request->performance_review_id);

        // Authorise: only the employee who owns this review may edit it.
        if ($performanceReview->employee_id !== auth()->user()->employee?->id) {
            abort(403, 'You are not authorised to update this development plan.');
        }

        DB::beginTransaction();

        try {
            $submittedIds = [];

            foreach ($request->devplans as $item) {
                $data = [
                    'performance_review_id' => $performanceReview->id,
                    'objective'             => trim($item['plan']),
                    'updated_by'            => auth()->id(),
                ];

                if (!empty($item['id'])) {
                    $plan = PerformanceProfessionalDevelopmentPlan::find($item['id']);
                    if ($plan && $plan->performance_review_id === $performanceReview->id) {
                        $plan->update($data);
                        $submittedIds[] = $plan->id;
                        continue;
                    }
                }

                $data['created_by'] = auth()->id();
                $newPlan = PerformanceProfessionalDevelopmentPlan::create($data);
                $submittedIds[] = $newPlan->id;
            }

            // Remove dev plans that were deleted in the UI.
            PerformanceProfessionalDevelopmentPlan::where('performance_review_id', $performanceReview->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();

            DB::commit();

            return response()->json([
                'type'    => 'success',
                'message' => 'Development plan saved successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type'    => 'error',
                'message' => 'Failed to save: ' . $e->getMessage(),
            ], 500);
        }
    }
}