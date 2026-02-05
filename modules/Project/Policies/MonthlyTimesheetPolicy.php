<?php

namespace Modules\Project\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Carbon\Carbon;
use Modules\Project\Models\TimeSheet;

class MonthlyTimesheetPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function submit(User $user, TimeSheet $timeSheet)
    {
        if ($timeSheet->requester_id !== $user->id) {
            return false;
        }

        // if (Carbon::now()->lte($timeSheet->end_date)) {
        //     return false;
        // }

        return in_array($timeSheet->status_id, [
            config('constant.CREATED_STATUS'),
            config('constant.RETURNED_STATUS')
        ]);
    }

    public function approve(User $user, TimeSheet $timeSheet)
    {
        return ($timeSheet->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $timeSheet->approver_id);
    }
}