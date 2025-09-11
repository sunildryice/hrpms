<?php

namespace Modules\ExitStaffClearanceRecord\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ExitStaffClearance\Models\StaffClearanceRecord;
use Modules\Privilege\Models\User;
use Modules\Privilege\Repositories\UserRepository;

class StaffClearanceRecordPolicy
{
    use HandlesAuthorization;

    private $users;
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = app(UserRepository::class);
    }

    public function edit(User $user, StaffClearanceRecord $record)
    {
        return $record->created_by == $user->id;
    }

}
