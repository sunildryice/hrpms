<?php

namespace Modules\GoodRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\GoodRequest\Models\GoodRequest;
use Modules\Privilege\Models\User;

class GoodRequestPolicy
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
     * Determine if the given good request can be approved by the user.
     *
     * @return bool
     */
    public function approve(User $user, GoodRequest $goodRequest)
    {
        if (empty($goodRequest->reviewer_id)) {
            return $goodRequest->status_id == config('constant.SUBMITTED_STATUS') &&
            $user->id == $goodRequest->approver_id &&
            empty($goodRequest->reviewer_id);
        } else {
            return $goodRequest->status_id == config('constant.VERIFIED_STATUS') && $user->id == $goodRequest->approver_id;
        }
    }

    public function assign(User $user, GoodRequest $goodRequest)
    {
        return $goodRequest->status_id == config('constant.APPROVED_STATUS') &&
            $user->id == $goodRequest->logistic_officer_id;
    }

    /**
     * Determine if the given good request's receiver note can be added by the user.
     *
     * @return bool
     */
    public function addReceiverNote(User $user, GoodRequest $goodRequest)
    {
        $requester = $goodRequest->receiver_id ?? $goodRequest->created_by;

        return in_array($goodRequest->status_id, [config('constant.ASSIGNED_STATUS')]) && empty($goodRequest->receiver_note) && $requester == $user->id;
    }

    /**
     * Determine if the given good request can be deleted by the user.
     *
     * @return bool
     */
    public function delete(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$goodRequest->requester_id, $goodRequest->created_by]);
    }

    /**
     * Determine if the given good request can be reviewed by the user.
     *
     * @return bool
     */
    public function review(User $user, GoodRequest $goodRequest)
    {
        return $goodRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $goodRequest->reviewer_id;
    }

    /**
     * Determine if the given good request can be updated by the user.
     *
     * @return bool
     */
    public function update(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$goodRequest->requester_id, $goodRequest->created_by]);
    }

    /**
     * Determine if the given approved good request can be viewed by the user.
     *
     * @return bool
     */
    public function viewApproved(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.ASSIGNED_STATUS')]);
    }

    /**
     * Determine if the given handovered good request can be viewed by the user.
     *
     * @return bool
     */
    public function handover(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.ASSIGNED_STATUS')]) &&
             $goodRequest->assignedItem->category->inventoryType->title == 'Non Consumable';
    }

    public function sendDirectDispatchRequest(User $user)
    {
        return $user->can('direct-dispatch-good-request');
    }

    public function approveDirectDispatchRequest(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.SUBMITTED_STATUS')]) &&
            $goodRequest->approver_id == $user->id &&
            $goodRequest->is_direct_dispatch == '1';
    }

    public function approveDirectAssignRequest(User $user, GoodRequest $goodRequest)
    {
        return in_array($goodRequest->status_id, [config('constant.SUBMITTED_STATUS')]) &&
            $goodRequest->approver_id == $user->id &&
            $goodRequest->is_direct_assign == '1';
    }

    public function receiveDirectAssignRequest(User $user, GoodRequest $goodRequest)
    {
        return (in_array($goodRequest->status_id, [config('constant.ASSIGNED_STATUS')]) &&
            $goodRequest->receiver_id == $user->id &&
            $goodRequest->is_direct_assign == '1' &&
            $goodRequest->received_at == null) || (
                $goodRequest->status_id == config('constant.APPROVED_STATUS') &&
            $goodRequest->receiver_id == $user->id &&
            $goodRequest->is_direct_dispatch == '1' &&
            $goodRequest->received_at == null
            );
    }
}
