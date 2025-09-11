<?php

namespace Modules\EmployeeExit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeExit\Models\employeeExitPayable;
use Modules\Privilege\Models\User;

class EmployeeExitPayablePolicy
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
     * Determine if the given advance request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\employeeExitPayable\Models\employeeExitPayable  $employeeExitPayable
     * @return bool
     */
    public function approve(User $user, employeeExitPayable $employeeExitPayable)
    {
        return ($employeeExitPayable->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $employeeExitPayable->reviewer_id) ||
                ($employeeExitPayable->status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $employeeExitPayable->approver_id);
    }

      /**
     * Determine if the given approved approve request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\employeeExitPayable\Models\employeeExitPayable  $employeeExitPayable
     * @return bool
     */
    public function viewApproved(User $user, employeeExitPayable $employeeExitPayable)
    {
        return in_array($employeeExitPayable->status_id, [config('constant.APPROVED_STATUS')]);
    }

    /**
     * Determine if the given advance request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\employeeExitPayable\Models\employeeExitPayable  $employeeExitPayable
     * @return bool
     */
    public function delete(User $user, employeeExitPayable $employeeExitPayable)
    {
        return in_array($employeeExitPayable->status_id, [config('constant.CREATED_STATUS')]) &&
                in_array($user->id, [$employeeExitPayable->created_by]);
    }

    /**
     * Determine if the given advance request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\employeeExitPayable\Models\employeeExitPayable  $employeeExitPayable
     * @return bool
     */
    public function update(User $user, employeeExitPayable $employeeExitPayable)
    {
        return in_array($employeeExitPayable->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
                date('Y-m-d') > $employeeExitPayable->exitHandOverNote->last_duty_date;
    }
}
