<?php

namespace Modules\ConstructionTrack\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ConstructionTrack\Models\ConstructionInstallment;
use Modules\Privilege\Models\User;

class ConstructionInstallmentPolicy
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

    public function edit(User $user, ConstructionInstallment $installment)
    {
        return in_array($installment->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
        $user->can('manage-settlement');
    }

    public function delete(User $user, ConstructionInstallment $installment)
    {
        return in_array($installment->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
        $user->can('manage-settlement');
    }

    public function submit(User $user, ConstructionInstallment $installment)
    {
        // return in_array($installment->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && $user->can('manage-construction');
        return false;
    }

    // public function review(User $user, ConstructionInstallment $installment)
    // {
    //     return in_array($installment->status_id, [config('constant.SUBMITTED_STATUS')]) && ($installment->reviewer_id == $user->id) && $user->can('verify-installment');
    // }

    // public function approve(User $user, ConstructionInstallment $installment)
    // {
    //     return in_array($installment->status_id, [config('constant.VERIFIED_STATUS')]) && ($installment->approver_id == $user->id) && $user->can('approve-installment');
    // }
}
