<?php

namespace Modules\Project\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Project\Models\ActivityStage;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\Enums\ActivityStatus;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpDate;

class ActivityImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCalculatedFormulas
{
    protected $project;
    protected array $stageCache = [];
    protected array $userCache = [];
    protected array $errors = [];
    protected int $rowNumber = 1;
    protected array $activityIdsByTitle = [];
    protected array $rowToInstanceIndex = [];

    public function __construct($project)
    {
        $this->project = $project;

        $this->stageCache = ActivityStage::pluck('id', 'title')
            ->mapWithKeys(fn($id, $title) => [strtolower(trim($title)) => $id])
            ->toArray();

        $this->userCache = \App\Models\User::pluck('id', 'full_name')
            ->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id])
            ->toArray();

        // Pre-load existing (for updates or parent lookup fallback)
        $existing = ProjectActivity::where('project_id', $project->id)
            ->get(['id', 'title'])
            ->groupBy(function ($item) {
                return strtolower(trim($item->title));
            });

        foreach ($existing as $titleKey => $items) {
            $this->activityIdsByTitle[$titleKey] = $items->pluck('id')->toArray();
        }
    }

    public function collection(Collection $rows)
    {
        $activitiesToUpsert = [];
        $parentAssignments = [];
        $memberAssignments = [];

        foreach ($rows as $row) {
            $this->rowNumber++;

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $title = trim($this->getValue($row, ['activity_name', 'activity name', 'activity-name']) ?? '');
            if (!$title) {
                continue;
            }

            $titleKey = strtolower($title);
            $stageName = $this->getValue($row, ['stage_name', 'stage-name']);
            $membersStr = $this->getValue($row, ['members']);
            $parentTitle = trim($this->getValue($row, ['parent_activity', 'parent-activity']) ?? '');
            $statusStr = $this->getValue($row, ['activity_status', 'status']);
            $levelRaw = $this->getValue($row, ['activity_level']);
            $startRaw = $this->getValue($row, ['start_date']);
            $endRaw = $this->getValue($row, ['end_date']);

            $startDate = $this->parseDate($startRaw);
            $endDate = $this->parseDate($endRaw);

            $stageId = $stageName ? ($this->stageCache[strtolower(trim($stageName))] ?? null) : null;

            $data = [
                'project_id' => $this->project->id,
                'title' => $title,
                'activity_stage_id' => $stageId,
                'activity_level' => $this->normalizeLevel($levelRaw),
                'start_date' => $startDate,
                'completion_date' => $endDate,
                'status' => $this->parseStatus($statusStr)->value,
                'updated_by' => auth()->id() ?? 1,
                'updated_at' => now(),
            ];

            // Store the row for later processing 
            $index = count($activitiesToUpsert);

            $activitiesToUpsert[$index] = [
                'data' => $data,
                'parent_title' => $parentTitle,
                'title' => $title,
                'title_key' => $titleKey,
            ];

            // Record parent and member assignments by index
            if ($parentTitle) {
                $parentAssignments[] = [
                    'index' => $index,
                    'parent_key' => strtolower(trim($parentTitle)),
                ];
            }

            if ($membersStr) {
                $memberAssignments[] = [
                    'index' => $index,
                    'user_ids' => $this->parseMembers($membersStr),
                ];
            }
        }

        // Error checking
        if (!empty($this->errors)) {
            $validator = Validator::make([], []);
            foreach ($this->errors as $error) {
                $validator->errors()->add("row_{$error['row']}.{$error['field']}", $error['message']);
            }
            throw new ValidationException($validator);
        }

        DB::transaction(function () use ($activitiesToUpsert, $memberAssignments) {

            // Load existing activities
            $existingActivities = ProjectActivity::where('project_id', $this->project->id)
                ->get(['id', 'title', 'parent_id']);

            $existingMap = [];

            foreach ($existingActivities as $act) {
                $key = strtolower(trim($act->title)) . '|' . ($act->parent_id ?? 'null');
                $existingMap[$key] = $act->id;
            }

            // Track all resolved IDs
            $resolvedIds = [];

            foreach ($activitiesToUpsert as $index => $item) {

                if (!empty($item['parent_title']))
                    continue;

                $data = $item['data'];
                $data['parent_id'] = null;

                $key = strtolower($data['title']) . '|null';

                if (isset($existingMap[$key])) {
                    // UPDATE
                    $activity = ProjectActivity::find($existingMap[$key]);
                    $activity->update($data);
                } else {
                    // CREATE
                    $activity = ProjectActivity::create($data);
                    $existingMap[$key] = $activity->id;
                }

                $resolvedIds[$index] = $activity->id;
            }

            foreach ($activitiesToUpsert as $index => $item) {

                if (empty($item['parent_title']))
                    continue;

                $parentTitle = strtolower(trim($item['parent_title']));
                $parentKey = $parentTitle . '|null';

                // $parentId = ProjectActivity::where('project_id', $this->project->id)
                //     ->whereRaw('LOWER(TRIM(title)) = ?', [$parentTitle])
                //     ->orderByDesc('id')
                //     ->value('id');

                $parentId = null;

                if (isset($existingMap[$parentKey])) {
                    $parentId = $existingMap[$parentKey];
                } else {
                    foreach ($existingMap as $key => $id) {
                        if (str_starts_with($key, $parentTitle . '|')) {
                            $parentId = $id;
                            break;
                        }
                    }
                }

                if (!$parentId)
                    continue;

                $data = $item['data'];
                $data['parent_id'] = $parentId;

                $key = strtolower($data['title']) . '|' . $parentId;

                if (isset($existingMap[$key])) {
                    // UPDATE
                    $activity = ProjectActivity::find($existingMap[$key]);
                    $activity->update($data);
                } else {
                    // CREATE
                    $activity = ProjectActivity::create($data);
                    $existingMap[$key] = $activity->id;
                }

                $resolvedIds[$index] = $activity->id;
            }

            foreach ($memberAssignments as $assign) {

                $activityId = $resolvedIds[$assign['index']] ?? null;

                if (!$activityId)
                    continue;

                $activity = ProjectActivity::find($activityId);

                if ($activity && !empty($assign['user_ids'])) {
                    // $activity->members()->sync($assign['user_ids']);
                    $activity->members()->syncWithoutDetaching($assign['user_ids']);
                }
            }
        });
    }

    private function getValue($row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim($row[$key]) !== '') {
                return trim($row[$key]);
            }
        }
        return null;
    }

    private function parseMembers(string $members): array
    {
        $names = preg_split('/[;,]+/', $members);
        $userIds = [];
        foreach ($names as $name) {
            $clean = strtolower(trim(preg_replace('/\s+/', ' ', $name)));
            if (!$clean)
                continue;
            if (isset($this->userCache[$clean])) {
                $userIds[] = $this->userCache[$clean];
            }
        }
        return array_values(array_unique($userIds));
    }

    private function parseStatus(?string $value): ActivityStatus
    {
        $value = strtolower(trim($value ?? ''));
        return match ($value) {
            'completed' => ActivityStatus::Completed,
            'under progress', 'in progress' => ActivityStatus::UnderProgress,
            'not started' => ActivityStatus::NotStarted,
            'no longer required', 'not required' => ActivityStatus::NoRequired,
            default => ActivityStatus::NotStarted,
        };
    }

    private function parseDate($value): ?Carbon
    {
        if (empty($value))
            return null;
        if (is_numeric($value)) {
            try {
                return Carbon::instance(PhpDate::excelToDateTimeObject($value));
            } catch (\Exception) {
                return null;
            }
        }
        try {
            return Carbon::parse($value);
        } catch (\Exception) {
            return null;
        }
    }

    private function normalizeLevel($level): string
    {
        $level = strtolower(trim($level ?? ''));
        return match ($level) {
            'theme', 'activity theme' => 'theme',
            'sub activity', 'sub-activity', 'sub_activity' => 'sub_activity',
            default => 'activity',
        };
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[] = [
            'row' => $this->rowNumber,
            'field' => $field,
            'message' => $message,
        ];
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