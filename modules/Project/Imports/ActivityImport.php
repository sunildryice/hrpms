<?php

namespace Modules\Project\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\ProjectActivity;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Modules\Project\Models\Enums\ActivityStatus;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ActivityImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    protected $project;
    protected $activityMap = [];
    protected $stageCache = [];
    protected $userCache = [];
    protected $existingActivityKeys = [];

    protected $errors = [];
    protected $rowNumber = 0;

    public function __construct($project)
    {
        $this->project = $project;
        $this->stageCache = ActivityStage::pluck('id', 'title')->toArray();
        $this->userCache = \App\Models\User::pluck('id', 'full_name')->toArray();

        $activities = ProjectActivity::where('project_id', $this->project->id)
            ->get(['id', 'title', 'activity_level', 'activity_stage_id']);

        foreach ($activities as $act) {
            $key = $this->makeCompositeKey(
                $act->title,
                $act->activity_level,
                $act->activity_stage_id
            );
            $this->existingActivityKeys[$key] = $act->id;
            $this->activityMap[$act->title] = $act->id;
        }
    }

    private function makeCompositeKey(string $title, ?string $level, ?int $stageId): string
    {
        return implode('|', [
            trim(strtolower($title)),
            trim(strtolower($level ?? 'activity')),
            $stageId ?? 'null'
        ]);
    }

    public function collection($rows)
    {
        DB::beginTransaction();

        try {
            $activitiesToInsert = [];
            $activitiesToUpdate = [];
            $memberSyncData = [];
            $parentRelations = [];


            foreach ($rows as $row) {
                $this->rowNumber++;

                if ($row->filter()->isEmpty()) {
                    continue;
                }

                $title = trim($row['activity_name'] ?? $row['activity-name'] ?? $row['activity name'] ?? '');
                $stage_name = trim($row['stage_name'] ?? $row['stage-name'] ?? '');
                $level_raw = $row['activity_level'] ?? '';
                $start_raw = $row['start_date'] ?? null;
                $end_raw = $row['end_date'] ?? null;
                $members_str = trim($row['members'] ?? '');
                $parent_title = trim($row['parent_activity'] ?? $row['parent-activity'] ?? '');
                $status_str = trim($row['activity_status'] ?? $row['activity-status'] ?? $row['status'] ?? '');

                if (empty($title) || empty($start_raw) || empty($end_raw)) {
                    continue;
                }

                $start_date = $this->parseDate($start_raw);
                $end_date = $this->parseDate($end_raw);

                if (!$start_date || !$end_date) {
                    $this->errors[] = [
                        'row' => $this->rowNumber,
                        'field' => 'date',
                        'message' => 'Invalid or missing start/end date format'
                    ];
                    continue;
                }

                $level = $this->normalizeActivityLevel($level_raw);
                $stage_id = $stage_name ? ($this->stageCache[$stage_name] ?? null) : null;

                if ($stage_name !== '' && $stage_id === null) {
                    $this->errors[] = [
                        'row' => $this->rowNumber,
                        'field' => 'stage_name',
                        'value' => $stage_name,
                        'message' => "Invalid stage: '$stage_name'. Must match an existing stage title."
                    ];
                    continue;
                }

                $status = $this->parseStatus($status_str);

                $compositeKey = $this->makeCompositeKey($title, $level, $stage_id);

                $data = [
                    'project_id' => $this->project->id,
                    'title' => $title,
                    'activity_stage_id' => $stage_id,
                    'activity_level' => $level,
                    'parent_id' => null,
                    'start_date' => $start_date,
                    'completion_date' => $end_date,
                    'status' => $status->value,
                    'created_by' => auth()->id() ?? 1,
                    'updated_by' => auth()->id() ?? 1,
                ];

                if (isset($this->existingActivityKeys[$compositeKey])) {
                    $id = $this->existingActivityKeys[$compositeKey];
                    $activitiesToUpdate[$id] = $data;
                    $this->activityMap[$title] = $id;
                } else {
                    $activitiesToInsert[] = array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->activityMap[$title] = null;
                }

                if ($members_str !== '') {
                    $parsed = $this->parseMembers($members_str);
                    $memberSyncData[$title] = $parsed;
                }

                if ($parent_title) {
                    $parentRelations[$title] = $parent_title;
                }
            }

            if (!empty($this->errors)) {
                $validator = Validator::make([], []);
                foreach ($this->errors as $error) {
                    $key = "row_{$error['row']}.{$error['field']}";
                    $validator->errors()->add($key, $error['message']);
                }
                throw new ValidationException($validator);
            }

            // Insert new activities
            if (!empty($activitiesToInsert)) {
                ProjectActivity::insert($activitiesToInsert);

                $titles = array_column($activitiesToInsert, 'title');
                $newActivities = ProjectActivity::where('project_id', $this->project->id)
                    ->whereIn('title', $titles)
                    ->pluck('id', 'title')
                    ->toArray();

                foreach ($newActivities as $title => $id) {
                    $this->activityMap[$title] = $id;
                }
            }

            // Update existing
            foreach ($activitiesToUpdate as $id => $data) {
                ProjectActivity::where('id', $id)->update($data);
            }

            // Parents
            $parentUpdates = [];
            foreach ($parentRelations as $childTitle => $parentTitle) {
                $childId = $this->activityMap[$childTitle] ?? null;
                $parentId = $this->activityMap[$parentTitle] ?? null;

                if ($childId && $parentId && $childId !== $parentId) {
                    $parentUpdates[] = ['id' => $childId, 'parent_id' => $parentId];
                }
            }

            foreach ($parentUpdates as $update) {
                ProjectActivity::where('id', $update['id'])
                    ->update(['parent_id' => $update['parent_id']]);
            }

            // Sync members – without timestamps
            if (!empty($memberSyncData)) {
                $activityIds = array_filter(array_values($this->activityMap));

                if (!empty($activityIds)) {
                    DB::table('project_activity_members')
                        ->whereIn('activity_id', $activityIds)
                        ->delete();
                }

                $bulkInsert = [];
                $seen = [];

                foreach ($memberSyncData as $title => $userIds) {
                    $activityId = $this->activityMap[$title] ?? null;
                    if (!$activityId) {
                        continue;
                    }

                    foreach ($userIds as $userId) {
                        $pair = $activityId . '-' . $userId;
                        if (!isset($seen[$pair])) {
                            $bulkInsert[] = [
                                'activity_id' => $activityId,
                                'user_id' => $userId,
                            ];
                            $seen[$pair] = true;
                        }
                    }
                }

                if (!empty($bulkInsert)) {
                    DB::table('project_activity_members')->insert($bulkInsert);
                }
            }

            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function parseStatus(string $value): ActivityStatus
    {
        $value = trim(strtolower($value));

        return match ($value) {
            'completed' => ActivityStatus::Completed,
            'under progress', 'in progress' => ActivityStatus::UnderProgress,
            'no longer required', 'no longer needed', 'not required' => ActivityStatus::NoRequired,
            'not started' => ActivityStatus::NotStarted,
            default => ActivityStatus::NotStarted,
        };
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(PhpDate::excelToDateTimeObject($value));
            } catch (\Exception) {
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Exception) {
            return null;
        }
    }

    private function normalizeActivityLevel($raw): string
    {
        $normalized = trim(strtolower($raw));

        return match ($normalized) {
            'theme', 'themes', 'activity_theme', 'activity theme' => 'theme',
            'activity', 'activities', 'act' => 'activity',
            'sub activity', 'sub-activity', 'sub_activity',
            'subactivity', 'sub activities', 'sub-activitys', 'sub act' => 'sub_activity',
            default => 'activity',
        };
    }

    private function parseMembers(string $namesCsv): array
    {
        $rawNames = preg_split('/[;,]\s*|\s{2,}/', trim($namesCsv), -1, PREG_SPLIT_NO_EMPTY);
        $names = array_map(fn($n) => trim(preg_replace('/\s+/', ' ', $n)), $rawNames);

        $ids = [];
        $lowerMap = [];

        foreach ($this->userCache as $fullName => $id) {
            $lowerMap[strtolower(trim($fullName))] = $id;
        }

        foreach ($names as $originalName) {
            if (empty($originalName)) {
                continue;
            }

            $cleanName = trim(preg_replace('/\s+/', ' ', $originalName));
            $lowerName = strtolower($cleanName);

            if (isset($this->userCache[$cleanName])) {
                $ids[] = $this->userCache[$cleanName];
                continue;
            }

            if (isset($lowerMap[$lowerName])) {
                $ids[] = $lowerMap[$lowerName];
                continue;
            }
        }

        return array_values(array_unique($ids));
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