<?php

namespace Modules\AdvanceRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\AdvanceRequest\Models\AdvanceRequest;
use Modules\Privilege\Models\User;

class AdvanceRequestPolicy
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
     * Determine if the given advance request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function approve(User $user, AdvanceRequest $advanceRequest)
    {
        return ($user->id == $advanceRequest->approver_id) &&
            in_array($advanceRequest->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

      /**
     * Determine if the given approved request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $AdvanceRequest
     * @return bool
     */
    public function viewApproved(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS'), config('constant.PAID_STATUS')]);
    }

    /**
     * Determine if the given advance request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function delete(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
        in_array($user->id, [$advanceRequest->requester_id, $advanceRequest->created_by]);
    }

    /**
     * Determine if the given advance request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest $advanceRequest
     * @return bool
     */
    public function print(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS'), config('constant.PAID_STATUS')]) &&
        (in_array($user->id, [$advanceRequest->created_by, $advanceRequest->reviewer_id, $advanceRequest->verifier_id, $advanceRequest->approver_id]) ||
        $user->can('view-approved-advance-request'));
    }

    /**
     * Determine if the given advance request can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function submit(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$advanceRequest->requester_id, $advanceRequest->created_by]) &&
            $advanceRequest->advanceRequestDetails->count();
    }

    /**
     * Determine if the given advance request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function update(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [1, 2]) && in_array($user->id, [$advanceRequest->requester_id, $advanceRequest->created_by]);
    }

    /**
     * Determine if the given advance request can be settled by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function createSettlement(User $user, AdvanceRequest $advanceRequest)
    {
        return in_array($advanceRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')])&&
            in_array($user->id, [$advanceRequest->requester_id, $advanceRequest->created_by]) &&
            !$advanceRequest->advanceSettlement;
    }

     /**
     * Determine if the given advance request can be verified by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\AdvanceRequest  $advanceRequest
     * @return bool
     */
    public function verify(User $user, AdvanceRequest $advanceRequest)
    {
        return ($advanceRequest->status_id == 3 && $user->id == $advanceRequest->verifier_id);
    }

    /**
     * Determine if the given adv can be closed by the user
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\AdvanceRequest $advanceRequest
     * @return bool
     */
    public function close(User $user, AdvanceRequest $advanceRequest)
    {
        return $advanceRequest->status_id == config('constant.APPROVED_STATUS') &&
            !$advanceRequest->advanceSettlement;
    }

    public function pay(User $user, AdvanceRequest $advanceRequest)
    {
        return is_null($advanceRequest->paid_at) &&  $user->can('pay-advance-settlement');
    }
}
