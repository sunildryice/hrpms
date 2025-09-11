<?php

namespace Modules\DistributionRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\DistributionRequest\Models\DistributionHandover;
use Modules\Privilege\Models\User;

class DistributionHandoverPolicy
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
     * Determine if the given distribution handover can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\DistributionRequest\Models\DistributionHandover $distributionHandover
     * @return bool
     */
    public function approve(User $user, DistributionHandover $distributionHandover)
    {
        return ($distributionHandover->status_id == config('constant.SUBMITTED_STATUS') &&
            $user->id == $distributionHandover->approver_id);
    }

    /**
     * Determine if the given distribution handover can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\DistributionRequest\Models\DistributionHandover $distributionHandover
     * @return bool
     */
    public function delete(User $user, DistributionHandover $distributionHandover)
    {
        return in_array($distributionHandover->status_id, [config('constant.CREATED_STATUS')]) &&
            in_array($user->id, [$distributionHandover->requester_id, $distributionHandover->created_by]);
    }

    /**
     * Determine if the given distribution handover can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\DistributionRequest\Models\DistributionHandover $distributionHandover
     * @return bool
     */
    public function update(User $user, DistributionHandover $distributionHandover)
    {
        return in_array($distributionHandover->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$distributionHandover->requester_id, $distributionHandover->created_by]);
    }

    /**
     * Determine if the given approved distribution handover can be printed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\DistributionRequest\Models\DistributionHandover $distributionHandover
     * @return bool
     */
    public function print(User $user, DistributionHandover $distributionHandover)
    {
        return
            // in_array($distributionHandover->status_id, [config('constant.APPROVED_STATUS'),config('constant.RECEIVED_STATUS'),config('constant.DISTRIBUTED_STATUS')]) &&
            in_array($user->id, [$distributionHandover->requester_id, $distributionHandover->created_by, $distributionHandover->approver_id]);
    }

    /**
     * Determine if the given handover can be received by the user
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\DistributionRequest\Models\DistributionHandover $distributionHandover
     * @return bool
     */
    public function receive(User $user, DistributionHandover $distributionHandover)
    {
        return in_array($distributionHandover->status_id, [config('constant.APPROVED_STATUS'),config('constant.RECEIVED_STATUS')]) &&
            in_array($user->id, [$distributionHandover->receiver_id]);
    }
}
