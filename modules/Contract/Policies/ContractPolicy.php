<?php

namespace Modules\Contract\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Contract\Models\Contract;
use Modules\Privilege\Models\User;

class ContractPolicy
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
     * Determine if the given contract can be amended by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Contract\Models\Contract  $contract
     * @return bool
     */
    public function amend(User $user, Contract $contract)
    {
        return true;
    }

    /**
     * Determine if the given contract can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Contract\Models\Contract  $contract
     * @return bool
     */
    public function delete(User $user, Contract $contract)
    {
        return !$contract->latestAmendment()->count();
    }

    /**
     * Determine if the given contract can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Contract\Models\Contract  $contract
     * @return bool
     */
    public function update(User $user, Contract $contract)
    {
        return !$contract->latestAmendment()->count();
    }
}
