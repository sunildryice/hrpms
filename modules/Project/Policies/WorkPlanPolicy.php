<?php

namespace Modules\Project\Policies;

use Modules\Privilege\Models\User;
use Modules\Project\Models\WorkPlan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;

class WorkPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, WorkPlan $workPlan)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, WorkPlan $workPlan)
    {
        return Carbon::now() < Carbon::parse($workPlan->from_date)->next(Carbon::TUESDAY)->endOfDay();
    }

    public function delete(User $user, WorkPlan $workPlan)
    {
        return $this->update($user, $workPlan);
    }

    public function updateStatus(User $user, WorkPlan $workPlan)
    {
        return Carbon::now() > Carbon::parse($workPlan->from_date)->next(Carbon::WEDNESDAY)->startOfDay();
    }
}
