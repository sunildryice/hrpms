<?php

namespace Modules\PaymentSheet\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\PaymentSheet\Models\PaymentSheetDetail;
use Modules\Privilege\Models\User;

class PaymentSheetDetailPolicy
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


    public function delete(User $user, PaymentSheetDetail $paymentSheetDetail)
    {
        return in_array($paymentSheetDetail->paymentSheet->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
    }

}