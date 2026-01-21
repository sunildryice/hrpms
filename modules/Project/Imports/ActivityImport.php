<?php

namespace Modules\Project\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Project\Models\ProjectActivity;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ActivityImport implements ToCollection, WithBatchInserts, WithCalculatedFormulas, WithChunkReading, WithHeadingRow
{
    protected $project;

    public function __construct($project)
    {
        $this->project = $project;
    }

    public function collection($rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $start = $this->parseDate($row['start_date'] ?? null);
                $end = $this->parseDate($row['end_date'] ?? null);

                if (!$start || !$end) {
                    continue;
                }

                ProjectActivity::updateOrCreate(
                    [
                        'project_id' => $this->project->id,
                        'title' => trim($row['activity_name'] ?? ''),
                        'activity_stage_id' => trim($row['stages'] ?? ''),
                        'activity_level' => trim($row['activity_level'] ?? ''),
                        'start_date' => $start,
                        'completion_date' => $end,
                    ]
                );
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            Log::info($e->getMessage());
            throw $e;
        }
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(PhpDate::excelToDateTimeObject($value));
            } catch (\Exception $e) {
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}