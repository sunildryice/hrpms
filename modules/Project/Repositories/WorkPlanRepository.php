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
        return WorkPlanDetail::create([
            'work_plan_id' => $workPlanId,
            'project_id' => $data['project_id'],
            'project_activity_id' => $data['activity_id'],
            'plan_tasks' => $data['planned_task'],
            'status' => $data['status'] ?? 'not_started',
        ]);
    }

    public function getWorkPlanDetails($workPlanId)
    {
        return WorkPlanDetail::with(['project', 'activity'])
            ->where('work_plan_id', $workPlanId);
    }

    public function findDetailById($id)
    {
        return WorkPlanDetail::with(['workPlan', 'project'])->findOrFail($id);
    }

    public function updateDetail($id, $data)
    {
        $detail = $this->findDetailById($id);
        $detail->update($data);
        return $detail;
    }

    public function deleteDetail($id)
    {
        $detail = $this->findDetailById($id);
        return $detail->delete();
    }
}
