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
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;

class ActivityImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    protected $project;
    protected $activityMap = [];
    protected $stageCache = [];
    protected $userCache = [];

    public function __construct($project)
    {
        $this->project = $project;

        // Pre-load stages into cache
        $this->stageCache = ActivityStage::pluck('id', 'title')->toArray();

        // Pre-load users into cache
        $this->userCache = \App\Models\User::pluck('id', 'full_name')->toArray();
    }

    public function collection($rows)
    {
        DB::beginTransaction();

        try {
            // Pre-load existing activities for this project
            $existingActivities = ProjectActivity::where('project_id', $this->project->id)
                ->pluck('id', 'title')
                ->toArray();

            $activitiesToInsert = [];
            $activitiesToUpdate = [];
            $memberSyncData = [];
            $parentRelations = [];

            // Process all rows and prepare bulk operations
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
                $parent_title = trim($row['parent_activity'] ?? $row['parent-activity'] ?? '');

                if (empty($title) || empty($start_raw) || empty($end_raw)) {
                    continue;
                }

                $start_date = $this->parseDate($start_raw);
                $end_date = $this->parseDate($end_raw);

                if (!$start_date) {
                    continue;
                }

                $stage_id = $this->stageCache[$stage_name] ?? null;

                $data = [
                    'project_id' => $this->project->id,
                    'title' => $title,
                    'activity_stage_id' => $stage_id,
                    'activity_level' => $level ?: null,
                    'parent_id' => null,
                    'start_date' => $start_date,
                    'completion_date' => $end_date,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ];

                if (isset($existingActivities[$title])) {
                    $activityId = $existingActivities[$title];
                    $activitiesToUpdate[$activityId] = $data;
                    $this->activityMap[$title] = $activityId;
                } else {
                    $activitiesToInsert[] = array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Store member sync data for later
                if ($members_str) {
                    $memberSyncData[$title] = $this->parseMembers($members_str);
                }

                // Store parent relationship for later
                if ($parent_title) {
                    $parentRelations[$title] = $parent_title;
                }
            }

            // Bulk insert new activities
            if (!empty($activitiesToInsert)) {
                ProjectActivity::insert($activitiesToInsert);

                // Reload to get IDs for newly inserted
                $newActivities = ProjectActivity::where('project_id', $this->project->id)
                    ->whereIn('title', array_column($activitiesToInsert, 'title'))
                    ->pluck('id', 'title')
                    ->toArray();

                $this->activityMap = array_merge($this->activityMap, $newActivities);
            }

            // Bulk update existing activities
            if (!empty($activitiesToUpdate)) {
                foreach ($activitiesToUpdate as $id => $data) {
                    ProjectActivity::where('id', $id)->update($data);
                }
            }

            // Update parent relationships in bulk
            if (!empty($parentRelations)) {
                $parentUpdates = [];
                foreach ($parentRelations as $childTitle => $parentTitle) {
                    $childId = $this->activityMap[$childTitle] ?? null;
                    $parentId = $this->activityMap[$parentTitle] ?? null;

                    if ($childId && $parentId && $childId !== $parentId) {
                        $parentUpdates[] = ['id' => $childId, 'parent_id' => $parentId];
                    }
                }

                // Batch update parent_ids
                foreach ($parentUpdates as $update) {
                    ProjectActivity::where('id', $update['id'])
                        ->update(['parent_id' => $update['parent_id']]);
                }
            }

            // Sync members in bulk
            if (!empty($memberSyncData)) {
                $memberSyncBulk = [];
                $processedPairs = []; // Track unique activity-user pairs

                foreach ($memberSyncData as $title => $userIds) {
                    $activityId = $this->activityMap[$title] ?? null;
                    if ($activityId && !empty($userIds)) {
                        foreach ($userIds as $userId) {
                            $pairKey = $activityId . '-' . $userId;

                            // Only add if this pair hasn't been added yet
                            if (!isset($processedPairs[$pairKey])) {
                                $memberSyncBulk[] = [
                                    'activity_id' => $activityId,
                                    'user_id' => $userId,
                                ];
                                $processedPairs[$pairKey] = true;
                            }
                        }
                    }
                }

                if (!empty($memberSyncBulk)) {
                    // Delete existing member relations for these activities
                    DB::table('project_activity_members')
                        ->whereIn('activity_id', array_values($this->activityMap))
                        ->delete();

                    // Insert new member relations
                    DB::table('project_activity_members')->insert($memberSyncBulk);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
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

    private function parseMembers(string $namesCsv): array
    {
        $names = array_filter(array_map('trim', explode(',', $namesCsv)));

        if (empty($names)) {
            return [];
        }

        $userIds = [];
        foreach ($names as $name) {
            if (isset($this->userCache[$name])) {
                $userIds[] = $this->userCache[$name];
            }
        }

        // Remove duplicates and reindex
        return array_values(array_unique($userIds));
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
