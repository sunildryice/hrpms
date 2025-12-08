<?php

namespace Modules\LieuLeave\Policies;


use App\Models\User;
use Modules\LieuLeave\Models\LieuLeave;

class LieuLeavePolicy
{
    public function view(User $user, LieuLeave $lieuLeave): bool
    {

        return $user->id === $lieuLeave->employee_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create lieu leave');
    }

    public function update(User $user, LieuLeave $lieuLeave): bool
    {
        return $user->id === $lieuLeave->employee_id && !$lieuLeave->is_approved;
    }

    public function delete(User $user, LieuLeave $lieuLeave): bool
    {
        return $user->id === $lieuLeave->employee_id && !$lieuLeave->is_approved;
    }
}
