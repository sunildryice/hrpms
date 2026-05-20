<?php

namespace Modules\PerformanceReview\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\PerformanceReview\Imports\KeyGoalReviewImport;

class KeyGoalReviewImportController extends Controller
{
    public function create(Request $request)
    {
        return view('PerformanceReview::import');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'attachment' => 'required|max:10240|mimes:xlsx',
        ], [
            'attachment.required' => 'Please choose the file.',
            'attachment.max'      => 'File size cannot exceed :max KB.',
            'attachment.mimes'    => 'Please upload an Excel (.xlsx) file.',
        ]);

        $file = $request->file('attachment');

        try {
            // ── 1. Load the workbook with PhpSpreadsheet ──────────────────────
            // We bypass Maatwebsite/Excel's sheet-dispatcher here so that we can
            // iterate over dynamically-named sheets without knowing their names
            // in advance.
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheetNames  = $spreadsheet->getSheetNames();

            if (empty($sheetNames)) {
                return response()->json([
                    'message' => 'The uploaded file contains no sheets.',
                ], 422);
            }

            // ── 2. Process each sheet inside a single transaction ─────────────
            $importer = new KeyGoalReviewImport();

            DB::beginTransaction();

            foreach ($sheetNames as $sheetName) {
                $worksheet = $spreadsheet->getSheetByName($sheetName);

                // Convert worksheet rows to a plain Collection, skipping the
                // header row (row 1) and any fully-empty rows.
                $rows = $this->extractRows($worksheet);

                if ($rows->isEmpty()) {
                    $importer->summary['skipped_sheets'][] = $sheetName . ' (empty)';
                    continue;
                }

                $importer->importSheetData($sheetName, $rows);
            }

            DB::commit();

            // ── 3. Build a human-friendly response ────────────────────────────
            $summary = $importer->summary;
            $message = sprintf(
                'Import complete. %d employee(s) processed — %d key goal(s) and %d development plan(s) imported.',
                $summary['employees_processed'],
                $summary['key_goals_imported'],
                $summary['dev_plans_imported']
            );

            if (! empty($summary['skipped_sheets'])) {
                $message .= ' Skipped sheets: ' . implode(', ', $summary['skipped_sheets']) . '.';
            }

            return response()->json([
                'message' => $message,
                'summary' => $summary,
                'errors'  => $importer->errors,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message'    => 'Import failed due to validation errors.',
                'errors'     => $e->errors(),
                'raw_errors' => $e->validator->errors()->all(),
            ], 422);

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('KeyGoalReviewImport failed: ' . $th->getMessage(), [
                'trace' => $th->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Failed to import key goals. Please check the file and try again.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Convert a PhpSpreadsheet worksheet into a Collection of plain arrays.
     *
     * Row 1 is treated as the header and skipped.
     * Fully-empty rows are also skipped.
     * Each item in the returned Collection is a 0-indexed array:
     *   [0] => SN, [1] => Key Goal title, [2] => Output/deliverables, [3] => PDP objective (may be null)
     *
     * @param  \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet  $worksheet
     * @return Collection<int, array<int, mixed>>
     */
    private function extractRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): Collection
    {
        $rows = new Collection();
        $highestRow = $worksheet->getHighestDataRow();

        // Start at row 2 to skip the header
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
            $rowData = [];

            // Columns A–D (indices 1–4 in PhpSpreadsheet)
            for ($colIndex = 1; $colIndex <= 4; $colIndex++) {
                $cell      = $worksheet->getCellByColumnAndRow($colIndex, $rowIndex);
                $rowData[] = $cell->getValue() !== null ? trim((string) $cell->getValue()) : null;
            }

            // Skip rows where all four cells are empty/null
            if (! array_filter($rowData, fn($v) => $v !== null && $v !== '')) {
                continue;
            }

            $rows->push($rowData);
        }

        return $rows;
    }
}