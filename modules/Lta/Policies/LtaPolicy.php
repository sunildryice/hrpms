<?php

namespace Modules\Lta\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Lta\Models\LtaContract;
use Modules\Privilege\Models\User;

class LtaPolicy
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

    public function delete(User $user, LtaContract $contract)
    {
        return true;
    }

    public function update(User $user, LtaContract $contract)
    {
        return true;
    }
}
