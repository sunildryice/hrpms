<?php

namespace Modules\PurchaseOrder\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\Privilege\Models\User;

class PurchaseOrderPolicy
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
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function approve(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')]) &&
            $user->id == $purchaseOrder->approver_id;
    }

    /**
     * Determine if the given purchase request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && in_array($user->id, [$purchaseOrder->created_by]);
    }

    /**
     * Determine if the given purchase order can be reviewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function review(User $user, PurchaseOrder $purchaseOrder)
    {
        return ($purchaseOrder->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $purchaseOrder->reviewer_id);
    }

    /**
     * Determine if the given purchase request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function update(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && in_array($user->id, [$purchaseOrder->created_by]);
    }

    /**
     * Determine if the given approved purchase order can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function viewApproved(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.APPROVED_STATUS'), config('constant.CANCELLED_STATUS')]);
    }

    /**
     * Determine if the grn can be created by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function createGrn(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.APPROVED_STATUS')]) &&
            $user->can('grn');
    }

    /**
     * Determine if the PO can be reverse approve by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function reverse(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$purchaseOrder->requester_id, $purchaseOrder->created_by]) &&
        $purchaseOrder->grns->count() == 0 && $purchaseOrder->paymentSheets->count() == 0;
    }



    /**
     * Determine if the PO can be cancel approve by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PurchaseOrder\Models\PurchaseOrder  $purchaseOrder
     * @return bool
     */
    public function cancel(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.APPROVED_STATUS')]) &&
        in_array($user->id, [$purchaseOrder->requester_id, $purchaseOrder->created_by]) &&
        $purchaseOrder->grns->count() == 0 && $purchaseOrder->paymentSheets->count() == 0;
    }

    public function approveCancel(User $user, PurchaseOrder $purchaseOrder)
    {
        return in_array($purchaseOrder->status_id, [config('constant.INIT_CANCEL_STATUS')]) &&
            $user->id == $purchaseOrder->approver_id;
    }

}
