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
        $activitiesToInsert = [];
        $parentAssignments = [];   
        $memberAssignments = [];   

        foreach ($rows as $row) {
            $this->rowNumber++;

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $title = $this->getValue($row, ['activity_name', 'activity name', 'activity-name']);
            if (!$title) {
                continue;
            }

            $title = trim($title);
            $titleKey = strtolower($title);

            $stageName   = $this->getValue($row, ['stage_name', 'stage-name']);
            $membersStr  = $this->getValue($row, ['members']);
            $parentTitle = $this->getValue($row, ['parent_activity', 'parent-activity']);
            $statusStr   = $this->getValue($row, ['activity_status', 'status']);
            $levelRaw    = $this->getValue($row, ['activity_level']);
            $startRaw    = $this->getValue($row, ['start_date']);
            $endRaw      = $this->getValue($row, ['end_date']);

            $startDate = $this->parseDate($startRaw);
            $endDate   = $this->parseDate($endRaw);

            $stageId = null;
            if ($stageName) {
                $stageKey = strtolower(trim($stageName));
                $stageId = $this->stageCache[$stageKey] ?? null;
                if (!$stageId) {
                    $this->addError('stage_name', "Invalid stage: {$stageName}");
                    continue;
                }
            }

             $data = [
                'project_id'        => $this->project->id,
                'title'             => $title,
                'activity_stage_id' => $stageId,
                'activity_level'    => $this->normalizeLevel($levelRaw),
                'start_date'        => $startDate,
                'completion_date'   => $endDate,
                'status'            => $this->parseStatus($statusStr)->value,
                'updated_by'        => auth()->id() ?? 1,
                'updated_at'        => now(),
            ];

            $data['created_by'] = auth()->id() ?? 1;
            $data['created_at'] = now();

            $activitiesToInsert[] = $data;

            $instanceIndex = ($this->rowToInstanceIndex[$titleKey] ?? 0);
            $this->rowToInstanceIndex[$titleKey] = $instanceIndex + 1;

            if (!empty($parentTitle)) {
                $parentAssignments[] = [
                    'title_key'      => $titleKey,
                    'instance_index' => $instanceIndex,
                    'parent_key'     => strtolower(trim($parentTitle)),
                    'row'            => $this->rowNumber,
                ];
            }

            if (!empty($membersStr)) {
                $memberAssignments[] = [
                    'title_key'      => $titleKey,
                    'instance_index' => $instanceIndex,
                    'user_ids'       => $this->parseMembers($membersStr),
                    'row'            => $this->rowNumber,
                ];
            }
        }

        if (!empty($this->errors)) {
            $validator = Validator::make([], []);
            foreach ($this->errors as $error) {
                $validator->errors()->add(
                    "row_{$error['row']}.{$error['field']}",
                    $error['message']
                );
            }
            throw new ValidationException($validator);
        }

        DB::transaction(function () use ($activitiesToInsert, $parentAssignments, $memberAssignments) {
            if (empty($activitiesToInsert)) {
                return;
            }

            // Insert all new activities
            ProjectActivity::insert($activitiesToInsert);

            // Fetch the newly inserted records, ordered by id (insertion order)
            $inserted = ProjectActivity::where('project_id', $this->project->id)
                ->where('created_at', '>=', now()->subMinute()) 
                ->orderBy('id', 'asc')
                ->get(['id', 'title', 'created_at']);

            $newIdsByTitle = $inserted->groupBy(function ($item) {
                return strtolower(trim($item->title));
            })->map(fn($group) => $group->pluck('id')->toArray());

            // Merge with existing
            foreach ($newIdsByTitle as $titleKey => $newIds) {
                $existing = $this->activityIdsByTitle[$titleKey] ?? [];
                $this->activityIdsByTitle[$titleKey] = array_merge($existing, $newIds);
            }

            foreach ($parentAssignments as $assign) {
                $titleKey = $assign['title_key'];
                $idx = $assign['instance_index'];
                $parentKey = $assign['parent_key'];

                $childIds = $this->activityIdsByTitle[$titleKey] ?? [];
                $childId = $childIds[$idx] ?? null;

                $parentIds = $this->activityIdsByTitle[$parentKey] ?? [];
                // Take the last (most recent) parent with that title
                $parentId = !empty($parentIds) ? end($parentIds) : null;

                if ($childId && $parentId && $childId !== $parentId) {
                    ProjectActivity::where('id', $childId)
                        ->update(['parent_id' => $parentId]);
                }
            }

            foreach ($memberAssignments as $assign) {
                $titleKey = $assign['title_key'];
                $idx = $assign['instance_index'];
                $userIds = $assign['user_ids'];

                $childIds = $this->activityIdsByTitle[$titleKey] ?? [];
                $activityId = $childIds[$idx] ?? null;

                if (!$activityId || empty($userIds)) {
                    continue;
                }

                DB::table('project_activity_members')
                    ->where('activity_id', $activityId)
                    ->delete();

                $insertRows = collect($userIds)->map(fn($uid) => [
                    'activity_id' => $activityId,
                    'user_id'     => $uid,
                ])->toArray();

                if (!empty($insertRows)) {
                    DB::table('project_activity_members')->insert($insertRows);
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
            if (!$clean) continue;
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
        if (empty($value)) return null;
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