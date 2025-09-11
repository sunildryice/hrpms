<?php

namespace Modules\WorkLog\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\WorkLog\Models\WorkPlanDailyLog;
use Modules\Privilege\Models\User;

class WorkPlanDailyLogPolicy
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
    * Determine if the given daily work log can be deleted by the user.
    *
    * @param  \Modules\Privilege\Models\User  $user
    * @param  \Modules\WorkLog\Models\WorkPlanDailyLog  $workPlanDailyLog
    * @return bool
    */
   public function delete(User $user, WorkPlanDailyLog $workPlanDailyLog)
   {
       return in_array($workPlanDailyLog->workPlan->status_id, [1, 2, 4]) && in_array($user->id, [ $workPlanDailyLog->workPlan->requester_id]);

   }

    /**
     * Determine if the given daily work log can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\WorkLog\Models\WorkPlanDailyLog  $workPlanDailyLog
     * @return bool
     */
    public function update(User $user, WorkPlanDailyLog $workPlanDailyLog)
    {
        return in_array($workPlanDailyLog->workPlan->status_id, [1, 2, 4]) && in_array($user->id, [ $workPlanDailyLog->workPlan->requester_id]);

    }
}
