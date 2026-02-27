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
    protected array $activityCache = [];
    protected array $errors = [];
    protected int $rowNumber = 1;

    public function __construct($project)
    {
        $this->project = $project;

        // Cache stages: title => id
        $this->stageCache = ActivityStage::pluck('id', 'title')
            ->mapWithKeys(fn ($id, $title) => [strtolower(trim($title)) => $id])
            ->toArray();

        // Cache users: lowercase full_name => id
        $this->userCache = \App\Models\User::pluck('id', 'full_name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id])
            ->toArray();

        // Cache existing activities by title
        $this->activityCache = ProjectActivity::where('project_id', $project->id)
            ->pluck('id', 'title')
            ->mapWithKeys(fn ($id, $title) => [strtolower(trim($title)) => $id])
            ->toArray();
    }

    public function collection(Collection $rows)
    {
        $activitiesToInsert = [];
        $activitiesToUpdate = [];
        $memberMap = []; 
        $parentMap = []; 

        foreach ($rows as $row) {
            $this->rowNumber++;

            if ($row->filter()->isEmpty()) {
                continue;
            }

            $title = $this->getValue($row, [
                'activity_name',
                'activity name',
                'activity-name'
            ]);

            if (!$title) {
                continue;
            }

            $titleKey = strtolower(trim($title));

            $stageName = $this->getValue($row, ['stage_name', 'stage-name']);
            $membersStr = $this->getValue($row, ['members']);
            $parentTitle = $this->getValue($row, ['parent_activity', 'parent-activity']);
            $statusStr = $this->getValue($row, ['activity_status', 'status']);
            $levelRaw = $this->getValue($row, ['activity_level']);
            $startRaw = $this->getValue($row, ['start_date']);
            $endRaw = $this->getValue($row, ['end_date']);

            $startDate = $this->parseDate($startRaw);
            $endDate = $this->parseDate($endRaw);

            if (!$startDate || !$endDate) {
                $this->addError('date', 'Invalid start or end date');
                continue;
            }

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
                'project_id' => $this->project->id,
                'title' => trim($title),
                'activity_stage_id' => $stageId,
                'activity_level' => $this->normalizeLevel($levelRaw),
                'start_date' => $startDate,
                'completion_date' => $endDate,
                'status' => $this->parseStatus($statusStr)->value,
                'updated_by' => auth()->id() ?? 1,
                'updated_at' => now(),
            ];

            // Update or Insert
            if (isset($this->activityCache[$titleKey])) {
                $id = $this->activityCache[$titleKey];
                $activitiesToUpdate[$id] = $data;
            } else {
                $data['created_by'] = auth()->id() ?? 1;
                $data['created_at'] = now();
                $activitiesToInsert[$titleKey] = $data;
            }

            // Members parsing 
            if (!empty($membersStr)) {
                $memberMap[$titleKey] = $this->parseMembers($membersStr);
            }

            // Parent relation
            if (!empty($parentTitle)) {
                $parentMap[$titleKey] = strtolower(trim($parentTitle));
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

        DB::transaction(function () use (
            $activitiesToInsert,
            $activitiesToUpdate,
            $memberMap,
            $parentMap
        ) {

            // Insert new activities
            if (!empty($activitiesToInsert)) {
                ProjectActivity::insert(array_values($activitiesToInsert));

                $titles = array_column($activitiesToInsert, 'title');

                $new = ProjectActivity::where('project_id', $this->project->id)
                    ->whereIn('title', $titles)
                    ->pluck('id', 'title');

                foreach ($new as $title => $id) {
                    $this->activityCache[strtolower(trim($title))] = $id;
                }
            }

            // Update existing activities
            foreach ($activitiesToUpdate as $id => $data) {
                ProjectActivity::where('id', $id)->update($data);
            }

            // Set parent IDs
            foreach ($parentMap as $childKey => $parentKey) {
                $childId = $this->activityCache[$childKey] ?? null;
                $parentId = $this->activityCache[$parentKey] ?? null;

                if ($childId && $parentId && $childId !== $parentId) {
                    ProjectActivity::where('id', $childId)
                        ->update(['parent_id' => $parentId]);
                }
            }

            //  MEMBER IMPORT 
            foreach ($memberMap as $titleKey => $userIds) {
                $activityId = $this->activityCache[$titleKey] ?? null;

                if (!$activityId || empty($userIds)) {
                    continue;
                }

                // Delete only this activity members 
                DB::table('project_activity_members')
                    ->where('activity_id', $activityId)
                    ->delete();

                $insertRows = [];
                foreach ($userIds as $userId) {
                    $insertRows[] = [
                        'activity_id' => $activityId,
                        'user_id' => $userId,
                    ];
                }

                DB::table('project_activity_members')->insert($insertRows);
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

            if (!$clean) {
                continue;
            }

            if (isset($this->userCache[$clean])) {
                $userIds[] = $this->userCache[$clean];
            }
            // unmatched names are ignored silently
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
        if (empty($value)) {
            return null;
        }

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