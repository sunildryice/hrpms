<?php

namespace Modules\Project\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Repositories\ProjectActivityRepository;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;

class ActivityImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    protected $project;
    protected $repository;
    protected $activityMap = [];

    public function __construct($project)
    {
        $this->project = $project;
        $this->repository = app(ProjectActivityRepository::class);
    }

    public function collection($rows)
    {
        DB::beginTransaction();

        try {
            // First pass: create / update activities without setting parent_id
            foreach ($rows as $row) {
                if ($row->filter()->isEmpty()) {
                    continue;
                }

                $title = trim($row['activity_name'] ?? $row['activity-name'] ?? $row['activity name'] ?? '');
                $stage_name = trim($row['stage_name'] ?? $row['stage-name'] ?? '');
                $level = $this->normalizeActivityLevel($row['activity_level'] ?? '');
                $start_raw = $row['start_date'] ?? null;
                $end_raw = $row['end_date'] ?? null;
                $members_str = trim($row['members'] ?? '');

                if (empty($title) || empty($start_raw) || empty($end_raw)) {
                    continue;
                }

                $start_date = $this->parseDate($start_raw);
                $end_date = $this->parseDate($end_raw);

                if (!$start_date) {
                    continue;
                }

                // Try to find existing activity by title + start date + project 
                $existing = ProjectActivity::where('project_id', $this->project->id)
                    ->where('title', $title)
                    // ->whereDate('start_date', $start_date->startOfDay())
                    // ->whereDate('completion_date', $end_date->endOfDay())
                    ->first();

                $stage_id = null;
                if ($stage_name) {
                    $stage = ActivityStage::where('title', $stage_name)->first();
                    $stage_id = $stage?->id;
                }

                $parent_id = null;

                $data = [
                    'project_id' => $this->project->id,
                    'title' => $title,
                    'activity_stage_id' => $stage_id,
                    'activity_level' => $level ?: null,
                    'parent_id' => $parent_id,
                    'start_date' => $start_date,
                    'completion_date' => $end_date,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];

                if ($existing) {
                    $activity = $this->repository->update($existing->id, $data);
                } else {
                    $activity = $this->repository->create($data);
                }

                if ($members_str && $activity) {
                    $this->syncMembersFromString($activity, $members_str);
                }

                $this->activityMap[$title] = $activity->id;
            }

            // Second pass: assign parent_id
            foreach ($rows as $row) {
                if ($row->filter()->isEmpty()) {
                    continue;
                }

                $title = trim($row['activity_name'] ?? $row['activity-name'] ?? $row['activity name'] ?? '');
                $parent_title = trim($row['parent_activity'] ?? $row['parent-activity'] ?? '');

                if (empty($title) || empty($parent_title)) {
                    continue;
                }

                $childId = $this->activityMap[$title] ?? null;
                $parentId = $this->activityMap[$parent_title] ?? null;

                if ($childId && $parentId && $childId !== $parentId) {
                    ProjectActivity::where('id', $childId)
                        ->update(['parent_id' => $parentId]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            dd($e);
            Log::error('Activity import failed', ['error' => $e->getMessage()]);
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

    private function normalizeActivityLevel($raw): string
    {
        $normalized = trim(strtolower($raw));

        return match ($normalized) {
            'theme', 'themes', 'activity_theme', 'activity theme' => 'theme',
            'activity', 'activities', 'act' => 'activity',
            'sub activity', 'sub-activity', 'sub_activity', 'subactivity', 'sub activities', 'sub-activitys', 'sub act' => 'sub_activity',
            default => 'activity',
        };
    }

    private function syncMembersFromString(ProjectActivity $activity, string $namesCsv): void
    {
        $names = array_filter(array_map('trim', explode(',', $namesCsv)));

        if (empty($names)) {
            $activity->members()->sync([]);
            return;
        }

        $userIds = \App\Models\User::whereIn('full_name', $names)
            ->pluck('id')
            ->toArray();

        $activity->members()->sync($userIds);
    }

    public function batchSize(): int
    {
        return 400;
    }

    public function chunkSize(): int
    {
        return 400;
    }
}
