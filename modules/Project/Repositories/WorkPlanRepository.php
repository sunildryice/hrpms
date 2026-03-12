<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Project\Models\WorkPlan;
use Modules\Project\Models\WorkPlanDetail;

class WorkPlanRepository extends Repository
{
    public function __construct(WorkPlan $workPlan)
    {
        $this->model = $workPlan;
    }

    public function createWorkPlan($data)
    {
        return $this->model->create($data);
    }

    public function findByDateAndEmployee($date, $employeeId)
    {

        return $this->model->where('employee_id', $employeeId)
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->first();
    }

    public function findOrCreateWorkPlan($employeeId, $fromDate, $toDate)
    {
        $workPlan = $this->findByDateAndEmployee($fromDate, $employeeId);

        if (!$workPlan) {
            $workPlan = $this->createWorkPlan([
                'employee_id' => $employeeId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]);
        }

        return $workPlan;
    }

    public function createWorkPlanDetail($workPlanId, $data)
    {
        $detail = WorkPlanDetail::create([
            'work_plan_id' => $workPlanId,
            // 'work_plan_date' => $data['work_plan_date'] ?? null,
            'project_id' => $data['project_id'],
            // 'project_activity_id' => $data['activity_id'],
            'plan_tasks' => $data['planned_task'],
            'status' => $data['status'] ?? 'not_started',
        ]);

        if (!empty($data['members'])) {
            $detail->members()->sync($data['members']);
        }

        return $detail;
    }

    public function getWorkPlanDetails($workPlanId)
    {
        return $this->model->with(['project', 'members'])
            ->where('work_plan_id', $workPlanId);
    }

    public function getUserWorkPlanDetailsByWeek($fromDate, $toDate, $userId)
    {
        return $this->model->with(['project', 'workPlan.employee', 'members'])
            ->whereHas('workPlan', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('from_date', $fromDate)
                    ->whereDate('to_date', $toDate);
            })
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
    }

    public function getUserWorkPlanDetails($fromDate, $toDate, $authUser)
    {
        $employeeId = $authUser->employee?->id ?? 0;

        return WorkPlanDetail::with(['project', 'activity', 'workPlan.employee', 'members'])
            ->whereHas(
                'workPlan',
                fn($q) =>
                $q->whereDate('from_date', $fromDate)
                    ->whereDate('to_date', $toDate)
                ->where('employee_id', $employeeId)
            );
    }

    public function getWeekSelectionForUser(int $userId, ?string $requestedWeekStart = null): array
    {
        $weekRanges = $this->getAvailableWeekRangesForUser($userId);
        $weeks = $this->buildWeekOptions($weekRanges);
        $defaultWeek = $weekRanges->first();

        $currentWeekStart = $this->resolveWeekStart($requestedWeekStart, $weeks, $defaultWeek);
        $selectedWeek = $weekRanges->first(function ($week) use ($currentWeekStart) {
            return $week->from_date->isSameDay($currentWeekStart);
        });
        $currentWeekEnd = $selectedWeek?->to_date?->copy() ?? $currentWeekStart->copy()->addDays(6);

        return [
            'weeks' => $weeks,
            'current_week_start' => $currentWeekStart,
            'current_week_end' => $currentWeekEnd,
            'selected_week' => $selectedWeek,
        ];
    }

    protected function getAvailableWeekRangesForUser(int $userId): Collection
    {
        $existingWeeks = $this->model
            ->select(['from_date', 'to_date'])
            ->whereHas('details.members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereNotNull('from_date')
            ->whereNotNull('to_date')
            ->orderBy('from_date', 'desc')
            ->get();

        return $existingWeeks->unique(function ($week) {
            return $week->from_date->format('Y-m-d');
        })->values();
    }

    protected function buildWeekOptions(Collection $weekRanges): array
    {
        return $weekRanges->mapWithKeys(function ($week) {
            $key = $week->from_date->format('Y-m-d');
            $label = $week->from_date->format('M j, Y') . ' - ' . $week->to_date->format('M j, Y');

            return [$key => $label];
        })->toArray();
    }

    protected function resolveWeekStart(?string $requestedWeekStart, array $weeks, ?WorkPlan $defaultWeek): Carbon
    {
        if ($requestedWeekStart && array_key_exists($requestedWeekStart, $weeks)) {
            return Carbon::parse($requestedWeekStart)->startOfDay();
        }

        if ($defaultWeek) {
            return $defaultWeek->from_date->copy();
        }

        return Carbon::now()->startOfWeek(Carbon::SUNDAY);
    }

    public function findDetailById($id)
    {
        return WorkPlanDetail::with(['workPlan', 'project', 'activity', 'members'])->findOrFail($id);
    }

    public function updateDetail($id, $data)
    {
        $detail = $this->findDetailById($id);

        $payload = [];

        if (array_key_exists('project_id', $data)) {
            $payload['project_id'] = $data['project_id'];
        }

        if (array_key_exists('activity_id', $data)) {
            $payload['project_activity_id'] = $data['activity_id'];
        }

        if (array_key_exists('planned_task', $data)) {
            $payload['plan_tasks'] = $data['planned_task'];
        }

        if (array_key_exists('work_plan_date', $data)) {
            $payload['work_plan_date'] = $data['work_plan_date'];
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $data['status'];
        }

        if (array_key_exists('reason', $data)) {
            $payload['reason'] = $data['reason'];
        }

        if (!empty($payload)) {
            $detail->update($payload);
        }

        if (array_key_exists('members', $data)) {
            $detail->members()->sync($data['members'] ?? []);
        }

        return $detail;
    }

    public function deleteDetail($id)
    {
        $detail = $this->findDetailById($id);
        return $detail->delete();
    }
}
