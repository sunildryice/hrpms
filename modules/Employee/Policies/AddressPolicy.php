<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\Address;
use Modules\Privilege\Models\User;

class AddressPolicy
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
     * @param  \Modules\Employee\Models\Address  $address
     * @return bool
     */
    public function create(User $user, Address $address)
    {
        return $user->employee_id || Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee address can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Address  $address
     * @return bool
     */
    public function delete(User $user, Address $address)
    {
        return $address->employee_id == $user->employee_id || Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee address can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Address  $address
     * @return bool
     */
    public function update(User $user, Address $address)
    {
        return $address->employee_id == $user->employee_id || Gate::allows('manage-employee');
    }
}
