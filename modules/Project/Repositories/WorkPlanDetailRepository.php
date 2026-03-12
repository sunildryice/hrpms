<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\WorkPlanDetail;

class WorkPlanDetailRepository extends Repository
{
    public function __construct(WorkPlanDetail $workPlanDetails)
    {
        $this->model = $workPlanDetails;
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

        return $this->model->with(['project', 'activity', 'workPlan.employee', 'members'])
            ->whereHas(
                'workPlan',
                fn($q) =>
                $q->whereDate('from_date', $fromDate)
                    ->whereDate('to_date', $toDate)
                ->where('employee_id', $employeeId)
            );
    }
}
