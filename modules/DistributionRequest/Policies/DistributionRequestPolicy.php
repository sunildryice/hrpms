<?php

namespace Modules\DistributionRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\DistributionRequest\Models\DistributionRequest;
use Modules\Privilege\Models\User;

class DistributionRequestPolicy
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
     * Determine if the given distribution request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\DistributionRequest\Models\DistributionRequest  $distributionRequest
     * @return bool
     */
    public function approve(User $user, DistributionRequest $distributionRequest)
    {
        return ($distributionRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $distributionRequest->reviewer_id) ||
            ($distributionRequest->status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $distributionRequest->approver_id);
    }

    /**
     * Determine if the given distribution request can be handover by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\DistributionRequest\Models\DistributionRequest  $distributionRequest
     * @return bool
     */
    public function createHandover(User $user, DistributionRequest $distributionRequest)
    {
        return $distributionRequest->status_id == config('constant.APPROVED_STATUS')
            && in_array($user->id, [$distributionRequest->requester_id, $distributionRequest->created_by]) &&
            !$distributionRequest->distributionHandover;
    }

    /**
     * Determine if the given distribution request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\DistributionRequest\Models\DistributionRequest  $distributionRequest
     * @return bool
     */
    public function delete(User $user, DistributionRequest $distributionRequest)
    {
        return in_array($distributionRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$distributionRequest->requester_id, $distributionRequest->created_by]);
    }

    /**
     * Determine if the given distribution request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\DistributionRequest\Models\DistributionRequest  $distributionRequest
     * @return bool
     */
    public function update(User $user, DistributionRequest $distributionRequest)
    {
        return in_array($distributionRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$distributionRequest->requester_id, $distributionRequest->created_by]);
    }

    /**
     * Determine if the given approved distribution request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\DistributionRequest\Models\DistributionRequest  $distributionRequest
     * @return bool
     */
    public function viewApproved(User $user, DistributionRequest $distributionRequest)
    {
        return in_array($distributionRequest->status_id, [config('constant.APPROVED_STATUS')]);
    }
}
