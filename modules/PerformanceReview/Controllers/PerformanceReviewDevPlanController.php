<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Master\Models\NepaliFiscalYear;
use Modules\PerformanceReview\Models\PerformanceProfessionalDevelopmentPlan;
use Modules\PerformanceReview\Models\PerformanceReview;

class PerformanceReviewDevPlanController extends Controller
{

    public function __construct(
        protected PerformanceReview $performanceReview,
        protected NepaliFiscalYear $nepaliFiscalYear
    ) {
        $this->performanceReview = $performanceReview;
        $this->nepaliFiscalYear = $nepaliFiscalYear;
    }

    public function index()
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'No employee record linked to your account.');
        }

        $currentFiscalYearId = $this->nepaliFiscalYear->getCurrentFiscalYearId();

        // Check if employee has any Annual Review (1) review in the current fiscal year.
        $hasAnnualReview = $this->performanceReview
            ->where('employee_id', $employee->id)
            ->where('review_type_id', config('constant.ANNUAL_REVIEW'))
            ->where('fiscal_year_id', $currentFiscalYearId)
            ->exists();

        if ($hasAnnualReview) {
            return view('PerformanceReview::DevPlan.index', [
                'keyGoalReview' => null,
                'devPlans' => collect(),
                'canAccessPDP' => false,
            ]);
        }

        // Find the latest approved Key Goals Review (review_type_id = 3) for the current fiscal year.
        $keyGoalReview = $this->performanceReview
            ->with(['developmentPlans', 'fiscalYear'])
            ->where('employee_id', $employee->id)
            ->where('review_type_id', config('constant.KEY_GOALS_REVIEW'))
            ->where('fiscal_year_id', $currentFiscalYearId)
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->orderByDesc('created_at')
            ->first();

        return view('PerformanceReview::DevPlan.index', [
            'keyGoalReview' => $keyGoalReview,
            'devPlans' => $keyGoalReview ? $keyGoalReview->developmentPlans : collect(),
            'canAccessPDP' => true,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'performance_review_id' => 'required|integer|exists:performance_reviews,id',
            'devplans' => 'required|array|min:1',
            'devplans.*.plan' => 'required|string|max:2000',
        ]);

        $performanceReview = $this->performanceReview->findOrFail($request->performance_review_id);

        // Authorise: only the employee who owns this review may edit it.
        if ($performanceReview->employee_id !== auth()->user()->employee?->id) {
            abort(403, 'You are not authorised to update this development plan.');
        }

        if ($performanceReview->review_type_id !== config('constant.KEY_GOALS_REVIEW') ||
            $performanceReview->status_id !== config('constant.APPROVED_STATUS') ||
            $performanceReview->fiscal_year_id !== $this->nepaliFiscalYear->getCurrentFiscalYearId()) {
            abort(403, 'This development plan cannot be updated.');
        }

        $hasAnnualReview = $this->performanceReview
            ->where('employee_id', $performanceReview->employee_id)
            ->where('review_type_id', config('constant.ANNUAL_REVIEW'))
            ->where('fiscal_year_id', $this->nepaliFiscalYear->getCurrentFiscalYearId())
            ->exists();

        if ($hasAnnualReview) {
            abort(403, 'Development plans cannot be modified after an Annual Review has been created for the current fiscal year.');
        }

        DB::beginTransaction();

        try {
            $submittedIds = [];

            foreach ($request->devplans as $item) {
                $data = [
                    'performance_review_id' => $performanceReview->id,
                    'objective' => trim($item['plan']),
                    'updated_by' => auth()->id(),
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
                'type' => 'success',
                'message' => 'Development plan saved successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to save: ' . $e->getMessage(),
            ], 500);
        }
    }
}