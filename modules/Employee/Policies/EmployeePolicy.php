<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\Employee;
use Modules\Privilege\Models\User;

class EmployeePolicy
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
     * Determine if the given employee address can be added by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Employee  $employee
     * @return bool
     */
    public function create(User $user, Employee $employee)
    {
        return Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee address can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Employee  $employee
     * @return bool
     */
    public function delete(User $user, Employee $employee)
    {
        return Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee address can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Employee  $employee
     * @return bool
     */
    public function update(User $user, Employee $employee)
    {
        return Gate::allows('manage-employee');
    }
}
