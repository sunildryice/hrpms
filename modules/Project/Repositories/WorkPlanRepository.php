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
}
