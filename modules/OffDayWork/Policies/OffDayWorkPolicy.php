<?php

namespace Modules\OffDayWork\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\OffDayWork\Models\OffDayWork;
use Modules\Privilege\Models\User;

class OffDayWorkPolicy
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

    public function update(User $user, OffDayWork $offDayWork)
    {
        $offDayWorkStatus = [
            config('constant.CREATED_STATUS'),
            config('constant.REJECTED_STATUS')
        ];
        return in_array($offDayWork->status_id, $offDayWorkStatus) && in_array($user->id, [$offDayWork->requester_id, $offDayWork->created_by]);
    }
}
