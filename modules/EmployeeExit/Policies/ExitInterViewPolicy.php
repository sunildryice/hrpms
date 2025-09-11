<?php

namespace Modules\EmployeeExit\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeExit\Models\ExitInterview;
use Modules\Privilege\Models\User;

class ExitInterViewPolicy
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

    public function approve(User $user, ExitInterview $exitInterview)
    {
        return in_array($exitInterview->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]) &&
        $user->id == $exitInterview->approver_id;
    }

    public function viewApproved(User $user, ExitInterview $exitInterview)
    {
        return in_array($exitInterview->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function delete(User $user, ExitInterview $exitInterview)
    {
        return in_array($exitInterview->status_id, [1]) && in_array($user->id, [$exitInterview->requester_id, $exitInterview->created_by]);
    }

    public function update(User $user, ExitInterview $exitInterview)
    {
        return in_array($exitInterview->status_id, [1, 2]) && in_array($user->employee_id, [$exitInterview->employee_id]);
    }
}
