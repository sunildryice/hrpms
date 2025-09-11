<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\EmployeeHour;
use Modules\Privilege\Models\User;

class EmployeeHourPolicy
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
     * @param  \Modules\Employee\Models\EmployeeHour  $hour
     * @return bool
     */
    public function create(User $user, EmployeeHour $hour)
    {
        return Gate::allows('manage-employee-hour');
    }

    /**
     * Determine if the given employee tenure can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\EmployeeHour  $hour
     * @return bool
     */
    public function update(User $user, EmployeeHour $hour)
    {
        return  $hour->employee->latestHour->id == $hour->id && Gate::allows('manage-employee-hour');
    }
}
