<?php

namespace Modules\EmployeeAttendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\Privilege\Models\User;

class AttendancePolicy
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

    public function delete(User $user, Attendance $attendance)
    {
        return $user->can('import-attendance') && 
        in_array($attendance->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
    }

    public function submit(User $user, Attendance $attendance)
    {
        return in_array($attendance->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$attendance->requester_id]);
    }

    public function review(User $user, Attendance $attendance)
    {
        return ($attendance->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $attendance->reviewer_id);   
    }

    public function approve(User $user, Attendance $attendance)
    {
        return ($attendance->status_id == config('constant.VERIFIED_STATUS') && $user->id == $attendance->approver_id);
    }

    public function import(User $user)
    {
        return $user->can('import-attendance');
    }

    public function amend(User $user, Attendance $attendance)
    {
        return in_array($attendance->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$attendance->requester_id]);
    }
}