<?php

namespace Modules\Contract\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Contract\Models\ContractAmendment;
use Modules\Privilege\Models\User;

class ContractAmendmentPolicy
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
     * Determine if the given amended contract can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Contract\Models\ContractAmendment  $contractAmendment
     * @return bool
     */
    public function delete(User $user, ContractAmendment $contractAmendment)
    {
        return $contractAmendment->contract->latestAmendment->id == $contractAmendment->id;
    }

    /**
     * Determine if the given amended contract can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Contract\Models\ContractAmendment  $contractAmendment
     * @return bool
     */
    public function update(User $user, ContractAmendment $contractAmendment)
    {
        return $contractAmendment->contract->latestAmendment->id == $contractAmendment->id;
    }
}
