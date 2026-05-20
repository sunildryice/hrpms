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
    /**
     * Errors accumulated during import, keyed by sheet (employee) name.
     *
     * @var array<string, string[]>
     */
    public array $errors = [];

    /**
     * Summary counts returned after a successful import.
     *
     * @var array{employees_processed: int, key_goals_imported: int, dev_plans_imported: int, skipped_sheets: string[]}
     */
    public array $summary = [
        'employees_processed'  => 0,
        'key_goals_imported'   => 0,
        'dev_plans_imported'   => 0,
        'skipped_sheets'       => [],
    ];

    /** @var int Current fiscal year id, resolved once in the constructor. */
    private int $currentFiscalYearId;

    /** @var int KEY_GOALS_REVIEW review_type_id (3). */
    private int $keyGoalsReviewTypeId;

    /** @var int Authenticated user id used for audit columns. */
    private int $authUserId;

    public function __construct()
    {
        $this->currentFiscalYearId  = app(NepaliFiscalYear::class)->getCurrentFiscalYearId();
        $this->keyGoalsReviewTypeId = config('constant.KEY_GOALS_REVIEW', 3);
        $this->authUserId           = auth()->id();
    }

    // -------------------------------------------------------------------------
    // WithMultipleSheets — one per-sheet importer for every sheet name
    // -------------------------------------------------------------------------

    /**
     * Return a map of sheet-name => sheet-importer.
     * Because we don't know sheet names in advance we use a dynamic approach:
     * we return $this as the handler for every sheet by implementing
     * WithMultipleSheets directly on this class and deferring to
     * onUnknownSheet for sheets we can't match.
     *
     * Actually we use a simpler pattern: return a single
     * AllSheetsImport that handles the raw PhpSpreadsheet workbook.
     *
     * @return array<string, mixed>
     */
    public function sheets(): array
    {
        // We register a catch-all sheet handler per sheet using a factory.
        // Maatwebsite/Excel resolves sheet handlers by (string) sheet name or
        // (int) sheet index.  Because we don't know names ahead of time we
        // return an empty array and use onUnknownSheet to process each sheet.
        return [];
    }

    // -------------------------------------------------------------------------
    // SkipsUnknownSheets — called for every sheet not listed in sheets()
    // -------------------------------------------------------------------------

    /**
     * Every sheet in the workbook lands here because sheets() returns [].
     * We process it inline.
     */
    public function onUnknownSheet($sheetName)
    {
        // $sheetName is the tab label, which is the employee's full_name.
        $this->processSheet($sheetName);
    }

    // -------------------------------------------------------------------------
    // Core processing logic
    // -------------------------------------------------------------------------

    /**
     * Read one sheet by name, resolve the employee + performance review,
     * then upsert key goals and development plans.
     */
    private function processSheet(string $sheetName): void
    {
        // We cannot easily read rows here via the Maatwebsite callback because
        // onUnknownSheet only provides the name.  The actual row-reading is
        // done by the companion per-sheet class below, called from
        // KeyGoalReviewImportController after loading all sheet data upfront.
        //
        // ── Design note ──────────────────────────────────────────────────────
        // Maatwebsite/Excel's WithMultipleSheets + SkipsUnknownSheets is
        // designed for *named* sheets.  For a fully dynamic multi-sheet import
        // the cleanest approach is to load the workbook with openpyxl / PhpSpreadsheet
        // directly in the controller, extract the data, then call this class'
        // importSheetData() method for each sheet.
        //
        // The controller therefore calls importSheetData() directly; this
        // onUnknownSheet hook is kept as a no-op fallback.
    }

    /**
     * Main entry point called by the controller for each sheet.
     *
     * @param  string              $sheetName  Excel tab label (= employee full_name).
     * @param  Collection<int, array<int, mixed>>  $rows  Rows excluding the header row.
     *                                                    Each row is a 0-indexed array:
     *                                                    [0]=SN, [1]=Key Goal, [2]=Output, [3]=PDP|null
     * @return void
     */
    public function importSheetData(string $sheetName, Collection $rows): void
    {
        // 1. Resolve employee by full_name (case-insensitive trim match)
        $employee = Employee::whereRaw('LOWER(TRIM(full_name)) = ?', [
            strtolower(trim($sheetName)),
        ])->first();

        if (! $employee) {
            $this->summary['skipped_sheets'][] = $sheetName;
            Log::warning("KeyGoalReviewImport: No employee found for sheet '{$sheetName}'.");
            return;
        }

        // 2. Find the approved Key Goals Review performance review for the
        //    current fiscal year.  We import into whatever status the review is
        //    in (not restricted to approved-only) so the manager can trigger
        //    the import at any stage.  Adjust the where clause if needed.
        $performanceReview = PerformanceReview::where('employee_id', $employee->id)
            ->where('review_type_id', $this->keyGoalsReviewTypeId)
            ->where('fiscal_year_id', $this->currentFiscalYearId)
            ->first();

        if (! $performanceReview) {
            $this->summary['skipped_sheets'][] = $sheetName;
            Log::warning(
                "KeyGoalReviewImport: No Key Goals Review found for employee '{$sheetName}' "
                . "(id={$employee->id}) in fiscal year id={$this->currentFiscalYearId}."
            );
            return;
        }

        $reviewId = $performanceReview->id;

        // 3. Separate key-goal rows from PDP rows.
        //    A row contributes a key goal if column 1 (Key Goals) is non-empty.
        //    A row contributes a PDP      if column 3 (PDP) is non-empty.
        $keyGoalsImported = 0;
        $devPlansImported = 0;

        // Delete existing 'current' key goals and dev plans for this review
        // so the import is idempotent (re-running replaces previous data).
        PerformanceReviewKeyGoal::where('performance_review_id', $reviewId)
            ->where('type', 'current')
            ->delete();

        PerformanceProfessionalDevelopmentPlan::where('performance_review_id', $reviewId)
            ->delete();

        // 4. Insert rows
        foreach ($rows as $row) {
            $keyGoalTitle = isset($row[1]) ? trim((string) $row[1]) : '';
            $outputDeliverables = isset($row[2]) ? trim((string) $row[2]) : '';
            $pdpObjective = isset($row[3]) ? trim((string) $row[3]) : '';

            // Insert key goal if title is present
            if ($keyGoalTitle !== '') {
                PerformanceReviewKeyGoal::create([
                    'performance_review_id' => $reviewId,
                    'title'                 => $keyGoalTitle,
                    'output_deliverables'   => $outputDeliverables ?: null,
                    'type'                  => 'current',
                    'created_by'            => $this->authUserId,
                    'updated_by'            => $this->authUserId,
                ]);
                $keyGoalsImported++;
            }

            // Insert PDP if objective is present
            if ($pdpObjective !== '') {
                PerformanceProfessionalDevelopmentPlan::create([
                    'performance_review_id' => $reviewId,
                    'objective'             => $pdpObjective,
                    'created_by'            => $this->authUserId,
                    'updated_by'            => $this->authUserId,
                ]);
                $devPlansImported++;
            }
        }

        $this->summary['employees_processed']++;
        $this->summary['key_goals_imported'] += $keyGoalsImported;
        $this->summary['dev_plans_imported']  += $devPlansImported;

        Log::info(
            "KeyGoalReviewImport: Sheet '{$sheetName}' — "
            . "{$keyGoalsImported} key goals, {$devPlansImported} dev plans imported."
        );
    }
}