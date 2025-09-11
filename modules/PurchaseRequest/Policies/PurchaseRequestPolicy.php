<?php

namespace Modules\PurchaseRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use Modules\Privilege\Models\User;

class PurchaseRequestPolicy
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
     * Determine if the given purchase request can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function approve(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            && $user->id == $purchaseRequest->approver_id;
    }

    /**
     * Determine if the grn can be created by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseRequest\Models\PurchaseRequest  $purchaseRequest
     * @return bool
     */
    public function createGrn(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
            $user->can('grn');
    }

    /**
     * Determine if the given purchase request can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function delete(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->created_by]);
    }

    /**
     * Determine if the given purchase request can be printed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function print(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS'), config('constant.CLOSED_STATUS')]) &&
            (in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->approver_id]) || $user->can('view-approved-purchase-request') || $user->can('manage-procurement'));
    }

    /**
     * Determine if the given purchase request can be replicated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function replicate(User $user, PurchaseRequest $purchaseRequest)
    {
        return $purchaseRequest->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->created_by]);
    }

    /**
     * Determine if the given purchase request can be reviewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function review(User $user, PurchaseRequest $purchaseRequest)
    {
        return ($purchaseRequest->status_id == config('constant.VERIFIED2_STATUS') && $user->id == $purchaseRequest->reviewer_id);
    }

    public function verify(User $user, PurchaseRequest $purchaseRequest)
    {
        return ($purchaseRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $purchaseRequest->budget_verifier_id);
    }

    /**
     * Determine if the given purchase request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function submit(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->created_by]) &&
            $purchaseRequest->purchaseRequestItems->count();
    }

    /**
     * Determine if the given purchase request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function update(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->created_by]);
    }

    /**
     * Determine if the given approved purchase request can be viewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function viewApproved(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.CLOSED_STATUS'), config('constant.AMENDED_STATUS')]);
    }

    /**
     * Determine if the given pr can me reviewed by the recommended user
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function reviewRecommended(User $user, PurchaseRequest $purchaseRequest)
    {
        return ($purchaseRequest->status_id == config('constant.RECOMMENDED_STATUS') &&
            $user->id == $purchaseRequest->verifier_id);
    }

    /**
     * Determine if the pr can be approved by the userk
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function approveRecommended(User $user, PurchaseRequest $purchaseRequest)
    {
        return($purchaseRequest->status_id == config('constant.RECOMMENDED2_STATUS') &&
                $user->id == $purchaseRequest->approver_id);
    }

    /**
     * Determine if the purchase order can be created by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function createOrder(User $user, PurchaseRequest $purchaseRequest)
    {
        return in_array($purchaseRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
            $user->can('purchase-order');
    }

     /**
     * Determine if the given purchase request can be amended by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param \Modules\PurchaseRequest\Models\PurchaseRequest $purchaseRequest
     * @return bool
     */
    public function amend(User $user, PurchaseRequest $purchaseRequest)
    {
        return $purchaseRequest->status_id == config('constant.APPROVED_STATUS') &&
            $purchaseRequest->purchaseOrderItems->isEmpty() &&
            $purchaseRequest->grns->isEmpty() &&
            in_array($user->id, [$purchaseRequest->requester_id, $purchaseRequest->created_by]);
    }

    public function specialUpdate(User $user, PurchaseRequest $purchaseRequest)
    {
        return $user->id == 24;
    }

    public function close(User $user, PurchaseRequest $purchaseRequest)
    {
        return $purchaseRequest->status_id == config('constant.APPROVED_STATUS') &&
            is_null($purchaseRequest->closed_at) &&
            in_array($user->id, $purchaseRequest->procurementOfficers->pluck('id')->toArray());
    }

    public function open(User $user, PurchaseRequest $purchaseRequest)
    {
        return $purchaseRequest->status_id == config('constant.CLOSED_STATUS') &&
            in_array($user->id, $purchaseRequest->procurementOfficers->pluck('id')->toArray());
    }
}
