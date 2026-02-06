<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
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
            'project_id' => $data['project_id'],
            'project_activity_id' => $data['activity_id'],
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
        return WorkPlanDetail::with(['project', 'activity', 'members'])
            ->where('work_plan_id', $workPlanId);
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
