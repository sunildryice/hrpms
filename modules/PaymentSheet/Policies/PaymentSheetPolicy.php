<?php

namespace Modules\PaymentSheet\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\PaymentSheet\Models\PaymentSheet;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentSheetPolicy
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
     * Determine if the given payment sheet can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function approve(User $user, PaymentSheet $paymentSheet)
    {
        return in_array($paymentSheet->status_id, [config('constant.VERIFIED_STATUS')]) && 
        $user->id == $paymentSheet->approver_id;
    }

    /**
     * Determine if the given payment sheet can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function delete(User $user, PaymentSheet $paymentSheet)
    {
        return in_array($paymentSheet->status_id, [config('constant.CREATED_STATUS')]) &&
            in_array($user->id, [$paymentSheet->requester_id, $paymentSheet->created_by]);
    }

    /**
     * Determine if the given payment sheet can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function submit(User $user, PaymentSheet $paymentSheet)
    {
        return in_array($paymentSheet->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$paymentSheet->requester_id, $paymentSheet->created_by]) &&
            $paymentSheet->paymentSheetDetails->count();
    }

    /**
     * Determine if the given payment sheet can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function update(User $user, PaymentSheet $paymentSheet)
    {
        return in_array($paymentSheet->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$paymentSheet->created_by]);
    }

    /**
     * Determine if the given approved payment sheet can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function viewApproved(User $user, PaymentSheet $paymentSheet)
    {
        return in_array($paymentSheet->status_id, [config('constant.APPROVED_STATUS'),config('constant.PAID_STATUS')]);
    }

    /**
     * Determine if the given advance request can be verified by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentSheet  $paymentSheet
     * @return bool
     */
    public function verify(User $user, PaymentSheet $paymentSheet)
    {
        return ($paymentSheet->status_id == 3 && $user->id == $paymentSheet->verifier_id);
    }


    /**
     * Determine if the user can review the recommended payment sheet.
     */
    public function reviewRecommended(User $user, PaymentSheet $paymentSheet)
    {
        return $paymentSheet->status_id == config('constant.RECOMMENDED_STATUS') && 
        $paymentSheet->reviewer_id == $user->id;
    }

    /**
     * Determine if the user can approve the recommended payment sheet.
     */
    public function approveRecommended(User $user, PaymentSheet $paymentSheet)
    {
        return $paymentSheet->status_id == config('constant.RECOMMENDED2_STATUS') && 
        $paymentSheet->approver_id == $user->id;
    }

     /**
     * Determine if the given request can make payment confirmation by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function pay(User $user, PaymentSheet $paymentSheet){
        return is_null($paymentSheet->paid_at) &&  Gate::allows('pay-payment-sheet');
    }

    public function amend(User $user, PaymentSheet $paymentSheet)
    {
        return $paymentSheet->status_id == config('constant.APPROVED_STATUS') &&
                $paymentSheet->created_by == $user->id &&
                is_null($paymentSheet->paid_at);
    }
}
