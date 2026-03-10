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
        $workPlanMonday = Carbon::parse($workPlan->from_date)->next(Carbon::FRIDAY)->endOfDay();
        return Carbon::now() < $workPlanMonday;

        $now = Carbon::now();
        $weekStart = Carbon::parse($workPlan->from_date)->startOfDay();

        $daysToSaturday = 6 - $weekStart->dayOfWeek;
        $weekEnd = $weekStart->copy()->addDays($daysToSaturday)->endOfDay();

        // 1. Past Week
        if ($now->gt($weekEnd)) {
            return false;
        }

        // 2. Future Week
        if ($now->lt($weekStart)) {
            return true;
        }

        // 3. Current Week (Inside the range)
        // Editable ONLY up to Monday.
        return $now->dayOfWeekIso <= 1;
    }

    public function delete(User $user, WorkPlan $workPlan)
    {
        return $this->update($user, $workPlan);
    }

    public function updateStatus(User $user, WorkPlan $workPlan)
    {
        $workPlanFriday = Carbon::parse($workPlan->from_date)->next(Carbon::FRIDAY)->startOfDay();
        return Carbon::now() > $workPlanFriday;

        $now = Carbon::now()->startOfDay();
        $start = Carbon::parse($workPlan->from_date)->startOfDay();

        // Use model's to_date if available
        if ($workPlan->to_date) {
            $end = Carbon::parse($workPlan->to_date)->endOfDay();
        } else {
            $daysToSaturday = 6 - $start->dayOfWeek;
            $end = $start->copy()->addDays($daysToSaturday)->endOfDay();
        }

        // 1. Current Week: Now is within the plan duration
        if ($now->between($start, $end)) {
            return $now->isFriday();
        }

        // 2. Previous Week: Plan ended recently (within the last ~8 days)
        if ($now->gt($end)) {
            return $now->diffInDays($end) <= 8;
        }

        return false;
    }
}
