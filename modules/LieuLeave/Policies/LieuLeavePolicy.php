<?php

namespace Modules\LieuLeave\Policies;



use Modules\LieuLeave\Models\LieuLeaveRequest;
use Modules\Privilege\Models\User;

class LieuLeavePolicy
{
    public function view(User $user, LieuLeaveRequest $lieuLeave): bool
    {

        return $user->id === $lieuLeave->employee_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create lieu leave');
    }

    public function update(User $user, LieuLeaveRequest $lieuLeave)
    {
        $leaveStatus = [
            config('constant.CREATED_STATUS'),
            config('constant.REJECTED_STATUS'),
        ];
        return in_array($lieuLeave->status_id, $leaveStatus) && in_array($user->id, [$lieuLeave->requester_id, $lieuLeave->created_by]);
    }

    public function delete(User $user, LieuLeaveRequest $lieuLeave): bool
    {
        return $user->id === $lieuLeave->employee_id && !$lieuLeave->is_approved;
    }
}
