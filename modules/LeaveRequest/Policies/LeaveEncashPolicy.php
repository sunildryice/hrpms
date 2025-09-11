<?php

namespace Modules\LeaveRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Modules\LeaveRequest\Models\LeaveEncash;
use Modules\Privilege\Models\User;

class LeaveEncashPolicy
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
     * Determine if the given leave encash request can be amended by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function amend(User $user, LeaveEncash $leaveEncash)
    {
        return $leaveEncash->status_id == 6 && !$leaveEncash->childLeaveEncash
        && in_array($user->id, [$leaveEncash->requester_id, $leaveEncash->created_by]);
    }

    /**
     * Determine if the given leave encash request can be reviewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function review(User $user, LeaveEncash $leaveEncash)
    {
        return ($leaveEncash->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $leaveEncash->reviewer_id);
    }

    /**
     * Determine if the given leave encash request can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function approve(User $user, LeaveEncash $leaveEncash)
    {
        return in_array($leaveEncash->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS'), config('constant.VERIFIED_STATUS')])
        && $user->id == $leaveEncash->approver_id;
    }

    /**
     * Determine if the given leave encash request can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function delete(User $user, LeaveEncash $leaveEncash)
    {
        return in_array($leaveEncash->status_id, [1, 2]) && in_array($user->id, [$leaveEncash->requester_id, $leaveEncash->created_by]);
    }

    public function viewApproved(User $user, LeaveEncash $leaveEncash)
    {
        return in_array($leaveEncash->status_id, [config('constant.APPROVED_STATUS'), config('constant.PAID_STATUS')]);
    }

    /**
     * Determine if the given leave encash request can be printed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function print(User $user, LeaveEncash $leaveEncash)
    {
        return (in_array($leaveEncash->status_id, [6, 16])
            &&
            (in_array($user->id, [$leaveEncash->created_by, $leaveEncash->reviewer_id, $leaveEncash->approver_id])
                ||
                $leaveEncash->employee_id == $user->employee_id));
    }

    /**
     * Determine if the given leave encash request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveEncash $leaveEncash
     * @return bool
     */
    public function update(User $user, LeaveEncash $leaveEncash)
    {
        return in_array($leaveEncash->status_id, [1, 2]) && in_array($user->id, [$leaveEncash->requester_id, $leaveEncash->created_by]);
    }

    public function pay(User $user, LeaveEncash $leaveEncash)
    {
        return is_null($leaveEncash->paid_at) && Gate::allows('pay-leave-encash');
    }
}
