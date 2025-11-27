<?php

namespace Modules\LeaveRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\LeaveRequest\Models\LeaveRequest;
use Modules\Privilege\Models\User;

class LeaveRequestPolicy
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
     * Determine if the given leave request can be amended by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveRequest $leaveRequest
     * @return bool
     */
    public function amend(User $user, LeaveRequest $leaveRequest)
    {
        return $leaveRequest->status_id == 6 && !$leaveRequest->childLeaveRequest
            && in_array($user->id, [$leaveRequest->requester_id, $leaveRequest->created_by]);
    }

    /**
     * Determine if the given leave request can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveRequest $leaveRequest
     * @return bool
     */
    public function approve(User $user, LeaveRequest $leaveRequest)
    {
        return in_array($leaveRequest->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            && $user->id == $leaveRequest->approver_id;
    }


    public function review(User $user, LeaveRequest $leaveRequest)
    {
        return in_array($leaveRequest->status_id, [config('constant.SUBMITTED_STATUS')])
            && $user->can('review-leave-request');
    }

    /**
     * Determine if the given leave request can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveRequest $leaveRequest
     * @return bool
     */
    public function delete(User $user, LeaveRequest $leaveRequest)
    {
        return in_array($leaveRequest->status_id, [1, 2]) && in_array($user->id, [$leaveRequest->requester_id, $leaveRequest->created_by]);
    }

    /**
     * Determine if the given leave request can be printed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveRequest $leaveRequest
     * @return bool
     */
    public function print(User $user, LeaveRequest $leaveRequest)
    {
        return ($leaveRequest->status_id == 6)
            &&
            (
                in_array($user->id, [$leaveRequest->created_by, $leaveRequest->reviewer_id, $leaveRequest->approver_id])
                ||
                $user->hasRole('Human Resource')
            );
    }

    /**
     * Determine if the given leave request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\LeaveRequest\Models\LeaveRequest $leaveRequest
     * @return bool
     */
    public function update(User $user, LeaveRequest $leaveRequest)
    {
        return in_array($leaveRequest->status_id, [1, 2]) && in_array($user->id, [$leaveRequest->requester_id, $leaveRequest->created_by]);
    }
}
