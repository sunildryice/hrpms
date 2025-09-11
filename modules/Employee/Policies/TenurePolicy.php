<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\Tenure;
use Modules\Privilege\Models\User;

class TenurePolicy
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
     * Determine if the given employee tenure can be added by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Tenure  $tenure
     * @return bool
     */
    public function create(User $user, Tenure $tenure)
    {
        return Gate::allows('manage-tenure');
    }

    /**
     * Determine if the given employee tenure can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Tenure  $tenure
     * @return bool
     */
    public function update(User $user, Tenure $tenure)
    {
        return true;
        // return $tenure->created_at->diffInDays(now()) <= 7 && $tenure->employee->latestTenure->id == $tenure->id && Gate::allows('manage-tenure');
    }
}
