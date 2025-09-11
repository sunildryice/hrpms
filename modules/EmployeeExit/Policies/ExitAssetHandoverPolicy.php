<?php

namespace Modules\EmployeeExit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeExit\Models\ExitAssetHandover;
use Modules\Privilege\Models\User;

class ExitAssetHandoverPolicy
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

    public function approve(User $user, ExitAssetHandover $exitAssetHandover)
    {
        return in_array($exitAssetHandover->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]) && 
        $user->id == $exitAssetHandover->approver_id;
    }

    public function viewApproved(User $user, ExitAssetHandover $exitAssetHandover)
    {
        return in_array($exitAssetHandover->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function delete(User $user, ExitAssetHandover $exitAssetHandover)
    {
        return in_array($exitAssetHandover->status_id, [1]) && in_array($user->id, [$exitAssetHandover->requester_id, $exitAssetHandover->created_by]);
    }

    public function update(User $user, ExitAssetHandover $exitAssetHandover)
    {
        return in_array($exitAssetHandover->status_id, [1, 2]) && in_array($user->employee_id, [$exitAssetHandover->employee_id]);
    }
}
