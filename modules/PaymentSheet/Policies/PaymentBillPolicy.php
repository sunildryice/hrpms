<?php

namespace Modules\PaymentSheet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PaymentSheet\Models\PaymentBill;
use Modules\Privilege\Models\User;

class PaymentBillPolicy
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
     * Determine if the given payment bill can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentBill  $paymentBill
     * @return bool
     */
    public function delete(User $user, PaymentBill $paymentBill)
    {
        return in_array($user->id, [$paymentBill->created_by]);
    }

    /**
     * Determine if the given payment bill can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\PaymentSheet\Models\PaymentBill  $paymentBill
     * @return bool
     */
    public function update(User $user, PaymentBill $paymentBill)
    {
        return in_array($user->id, [$paymentBill->created_by]) && $paymentBill->paymentSheetDetails->count() == 0;
    }
}
