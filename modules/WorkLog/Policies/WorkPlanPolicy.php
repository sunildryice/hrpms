<?php

namespace Modules\WorkLog\Policies;

use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\WorkLog\Models\WorkPlan;
use Modules\Privilege\Models\User;

class WorkPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given work log can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function approve(User $user, WorkPlan $workPlan)
    {
        return in_array($workPlan->status_id, [3, 4]) && in_array($user->id, [$workPlan->reviewer_id, $workPlan->approver_id]);
    }

    /**
     * Determine if the given monthly work log can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function addEditDailyLog(User $user, WorkPlan $workPlan)
    {
        return in_array($workPlan->status_id, [1, 2, 4]) && in_array($user->id, [$workPlan->requester_id]);
    }

    /**
     * Determine if the given monthly work log can be edited by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function edit(User $user, WorkPlan $workPlan)
    {
        return in_array($workPlan->status_id, [1, 2]) && in_array($user->id, [$workPlan->requester_id]) && $workPlan->workPlanDailyLog->count() == 0;
    }

    /**
     * Determine if the given monthly work log can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function delete(User $user, WorkPlan $workPlan)
    {
        return in_array($workPlan->status_id, [1]) && in_array($user->id, [$workPlan->requester_id]) && $workPlan->workPlanDailyLog->count() == 0;
    }

    /**
     * Determine if the given work log can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function list(User $user, WorkPlan $workPlan)
    {
        return in_array($user->id, [$workPlan->requester_id, $workPlan->reviewer_id, $workPlan->approver_id]);
    }

    /**
     * Determine if the given Worklog can be printed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function print(User $user, WorkPlan $workPlan)
    {
        return ($workPlan->status_id == 6 && in_array($user->id, [$workPlan->reviewer_id, $workPlan->requester_id, $workPlan->approver_id]))
            || $user->can('view-approved-work-log');
    }

    /**
     * Determine if the given work log can be submitted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @param \Modules\WorkLog\Models\WorkPlanDailyLog $workPlanDailyLog
     * @return bool
     */
    public function submit(User $user, WorkPlan $workPlan)
    {
        $employeeExitCreated = isset($user->employee->exitHandOverNote->id);
        $validStatus = in_array($workPlan->status_id, [config('constant.CREATED_STATUS'),
                config('constant.RETURNED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            && in_array($user->id, [$workPlan->requester_id]) && $workPlan->workPlanDailyLog->count() > 0;

        if ($employeeExitCreated) {
            $exitHandoverNote = $user->employee->exitHandOverNote;
            $lastDutyDate = $exitHandoverNote->last_duty_date->format('Y-m-d');
            $lastDutyTimestamp = strtotime($exitHandoverNote->last_duty_date);

            if (date('m', $lastDutyTimestamp) == $workPlan->month) {
                $firstDayOfMonth = date('Y-m-01', $lastDutyTimestamp);
                $firstDayOfMonth = Carbon::parse($firstDayOfMonth);

                return $validStatus; // && date('Y-m-d') >= $lastDutyDate;
            }

            // if($exitHandoverNote->last_duty_date->subMonthNoOverflows()->format('m') == $workPlan->month) {
            //     return $validStatus;
            // }

        }

        return $validStatus && date('Y-m-d') >= $workPlan->getLastDayOfMonth();
    }

    /**
     * Determine if the given monthly work log can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function update(User $user, WorkPlan $workPlan)
    {
        return in_array($workPlan->status_id, [1, 2]) && in_array($user->id, [$workPlan->requester_id]);
    }

    /**
     * Determine if the given work log can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\WorkLog\Models\WorkPlan $workPlan
     * @return bool
     */
    public function view(User $user, WorkPlan $workPlan)
    {
        return in_array($user->id, [$workPlan->reviewer_id, $workPlan->approver_id]);
    }
}
