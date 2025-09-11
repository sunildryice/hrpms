<?php

namespace Modules\Employee\Policies;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Employee\Models\PaymentMaster;
use Modules\Privilege\Models\User;

class PaymentMasterPolicy
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
     * Determine if the given employee address can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\PaymentMaster  $paymentMaster
     * @return bool
     */
    public function delete(User $user, PaymentMaster $paymentMaster)
    {
        return $paymentMaster->employee_id == $user->employee_id;
    }

    /**
     * Determine if the given employee address can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Employee\Models\PaymentMaster  $paymentMaster
     * @return bool
     */
    public function update(User $user, PaymentMaster $paymentMaster)
    {
        return $paymentMaster->employee->paymentMasters->sortByDesc('start_date')->first()->id == $paymentMaster->id;
    }
}
