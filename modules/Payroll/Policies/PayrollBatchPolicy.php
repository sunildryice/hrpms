<?php

namespace Modules\Payroll\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Payroll\Models\PayrollBatch;
use Modules\Privilege\Models\User;

class PayrollBatchPolicy
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
     * Determine if the given payroll batch can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\Payroll\Models\PayrollBatch $paymentBatch
     * @return bool
     */
    public function delete(User $user, PayrollBatch $paymentBatch)
    {
        return in_array($paymentBatch->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$paymentBatch->created_by]) && $paymentBatch->id == PayrollBatch::max('id');
    }

    /**
     * Determine if the given payroll batch can be processed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\Payroll\Models\PayrollBatch $paymentBatch
     * @return bool
     */
    public function process(User $user, PayrollBatch $paymentBatch)
    {
        return in_array($paymentBatch->status_id , [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
        && $paymentBatch->id == PayrollBatch::max('id');
    }

    /**
     * Determine if the given payroll batch can be submitted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\Payroll\Models\PayrollBatch $paymentBatch
     * @return bool
     */
    public function submit(User $user, PayrollBatch $paymentBatch)
    {
        return in_array($paymentBatch->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && $paymentBatch->sheets->count() > 0;
    }
}
