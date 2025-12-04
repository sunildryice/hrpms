<?php

namespace Modules\WorkFromHome\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Privilege\Models\User;
use Modules\WorkFromHome\Models\WorkFromHome;

class WorkFromHomePolicy
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

    public function update(User $user, WorkFromHome $workFromHome)
    {
        $workFromHomeStatus = [
            config('constant.CREATED_STATUS'),
            config('constant.REJECTED_STATUS')
        ];
        return in_array($workFromHome->status_id, $workFromHomeStatus) && in_array($user->id, [$workFromHome->requester_id, $workFromHome->created_by]);
    }
}
