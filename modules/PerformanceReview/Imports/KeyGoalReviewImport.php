<?php

namespace Modules\PerformanceReview\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\NepaliFiscalYear;
use Modules\PerformanceReview\Models\PerformanceProfessionalDevelopmentPlan;
use Modules\PerformanceReview\Models\PerformanceReview;
use Modules\PerformanceReview\Models\PerformanceReviewKeyGoal;

class KeyGoalReviewImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public array $errors = [];

    public array $summary = [
        'employees_processed' => 0,
        'key_goals_imported' => 0,
        'dev_plans_imported' => 0,
        'skipped_sheets' => [],
    ];

    private int $currentFiscalYearId;

    private int $keyGoalsReviewTypeId;

    private int $authUserId;

    public function __construct()
    {
        $this->currentFiscalYearId = app(NepaliFiscalYear::class)->getCurrentFiscalYearId();
        $this->keyGoalsReviewTypeId = config('constant.KEY_GOALS_REVIEW', 3);
        $this->authUserId = auth()->id();
    }

    public function sheets(): array
    {
        return [];
    }

    public function onUnknownSheet($sheetName)
    {
        $this->processSheet($sheetName);
    }

    private function processSheet(string $sheetName): void
    {
    }

    public function importSheetData(string $sheetName, Collection $rows): void
    {
        $employee = Employee::whereRaw('LOWER(TRIM(full_name)) = ?', [
            strtolower(trim($sheetName)),
        ])->first();

        if (!$employee) {
            $this->summary['skipped_sheets'][] = $sheetName;
            Log::warning("KeyGoalReviewImport: No employee found for sheet '{$sheetName}'.");
            return;
        }

        $performanceReview = PerformanceReview::where('employee_id', $employee->id)
            ->where('review_type_id', $this->keyGoalsReviewTypeId)
            ->where('fiscal_year_id', $this->currentFiscalYearId)
            ->first();

        if (!$performanceReview) {
            $this->summary['skipped_sheets'][] = $sheetName;
            Log::warning(
                "KeyGoalReviewImport: No Key Goals Review found for employee '{$sheetName}' "
                . "(id={$employee->id}) in fiscal year id={$this->currentFiscalYearId}."
            );
            return;
        }

        $reviewId = $performanceReview->id;
        $keyGoalsImported = 0;
        $devPlansImported = 0;

        // Load existing records indexed by their position (1-based) for matching
        $existingKeyGoals = PerformanceReviewKeyGoal::where('performance_review_id', $reviewId)
            ->where('type', 'current')
            ->orderBy('id')
            ->get()
            ->values();

        $existingDevPlans = PerformanceProfessionalDevelopmentPlan::where('performance_review_id', $reviewId)
            ->orderBy('id')
            ->get()
            ->values();

        $keyGoalIndex = 0;
        $devPlanIndex = 0;

        foreach ($rows as $row) {
            $keyGoalTitle = isset($row[1]) ? trim((string) $row[1]) : '';
            $outputDeliverables = isset($row[2]) ? trim((string) $row[2]) : '';
            $pdpObjective = isset($row[3]) ? trim((string) $row[3]) : '';

            if ($keyGoalTitle !== '') {
                if (isset($existingKeyGoals[$keyGoalIndex])) {
                    // Update only the importable fields, preserve everything else
                    $existingKeyGoals[$keyGoalIndex]->update([
                        'title' => $keyGoalTitle,
                        'output_deliverables' => $outputDeliverables ?: null,
                        'updated_by' => $this->authUserId,
                    ]);
                } else {
                    // No existing record at this position — create a new one
                    PerformanceReviewKeyGoal::create([
                        'performance_review_id' => $reviewId,
                        'title' => $keyGoalTitle,
                        'output_deliverables' => $outputDeliverables ?: null,
                        'type' => 'current',
                        'created_by' => $this->authUserId,
                        'updated_by' => $this->authUserId,
                    ]);
                }
                $keyGoalIndex++;
                $keyGoalsImported++;
            }

            if ($pdpObjective !== '') {
                if (isset($existingDevPlans[$devPlanIndex])) {
                    // Update only 'objective', preserve 'activity' and other fields
                    $existingDevPlans[$devPlanIndex]->update([
                        'objective' => $pdpObjective,
                        'updated_by' => $this->authUserId,
                    ]);
                } else {
                    PerformanceProfessionalDevelopmentPlan::create([
                        'performance_review_id' => $reviewId,
                        'objective' => $pdpObjective,
                        'created_by' => $this->authUserId,
                        'updated_by' => $this->authUserId,
                    ]);
                }
                $devPlanIndex++;
                $devPlansImported++;
            }
        }

        $this->summary['employees_processed']++;
        $this->summary['key_goals_imported'] += $keyGoalsImported;
        $this->summary['dev_plans_imported'] += $devPlansImported;

        Log::info(
            "KeyGoalReviewImport: Sheet '{$sheetName}' — "
            . "{$keyGoalsImported} key goals, {$devPlansImported} dev plans imported."
        );
    }
}