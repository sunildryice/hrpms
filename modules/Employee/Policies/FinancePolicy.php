<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\Finance;
use Modules\Privilege\Models\User;

class FinancePolicy
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
     * Determine if the given employee finance can be added by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Finance  $finance
     * @return bool
     */
    public function create(User $user, Finance $finance)
    {
        return Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee finance can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Finance  $finance
     * @return bool
     */
    public function delete(User $user, Finance $finance)
    {
        return Gate::allows('manage-employee');
    }

    /**
     * Determine if the given employee finance can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\Finance  $finance
     * @return bool
     */
    public function update(User $user, Finance $finance)
    {
        return Gate::allows('manage-employee');
    }
}
