<?php

namespace Modules\EmployeeRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\EmployeeRequest\Models\EmployeeRequest;
use Modules\Privilege\Models\User;

class EmployeeRequestPolicy
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
     * Determine if the given employee request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest  $employeeRequest
     * @return bool
     */
    public function approve(User $user, EmployeeRequest $employeeRequest)
    {
        return ($user->can('approve-employee-requisition') || $user->can('approve-recommended-employee-requisition')) &&
        $this->approveVerified($user, $employeeRequest) || $this->approveRecommended($user, $employeeRequest);
    }

    public function approveRecommended(User $user, EmployeeRequest $employeeRequest)
    {
        $perm = (in_array($employeeRequest->status_id, [config('constant.RECOMMENDED_STATUS')]) && $user->id == $employeeRequest->approver_id) ||
        (!isset($employeeRequest->reviewer_id) && isset($employeeRequest->approver_id) && $employeeRequest->status_id == config('constant.SUBMITTED_STATUS'));
        return $perm;
    }

    public function approveVerified(User $user, EmployeeRequest $employeeRequest)
    {
        $perm = (in_array($employeeRequest->status_id, [config('constant.VERIFIED_STATUS')]) && $user->id == $employeeRequest->approver_id);
        return $perm;
    }

    /**
     * Determine if the given employee request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest  $employeeRequest
     * @return bool
     */
    public function delete(User $user, EmployeeRequest $employeeRequest)
    {
        return in_array($employeeRequest->status_id, [config('constant.CREATED_STATUS')]) &&
            in_array($user->id, [$employeeRequest->requester_id, $employeeRequest->created_by]);
    }

    /**
     * Determine if the given employee request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest $employeeRequest
     * @return bool
     */
    public function print(User $user, EmployeeRequest $employeeRequest)
    {
        return ($employeeRequest->status_id == config('constant.APPROVED_STATUS') && ($employeeRequest->created_by == $user->id || $user->can('view-approved-employee-requisition')));
    }

    /**
     * Determine if the given employee request can be reviewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest  $employeeRequest
     * @return bool
     */
    public function review(User $user, EmployeeRequest $employeeRequest)
    {
        return ($employeeRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $employeeRequest->reviewer_id);
    }

    /**
     * Determine if the given employee request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest  $employeeRequest
     * @return bool
     */
    public function update(User $user, EmployeeRequest $employeeRequest)
    {
        return in_array($employeeRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$employeeRequest->requester_id, $employeeRequest->created_by]);
    }

    /**
     * Determine if the given approved employee request can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\EmployeeRequest\Models\EmployeeRequest  $employeeRequest
     * @return bool
     */
    public function viewApproved(User $user, EmployeeRequest $employeeRequest)
    {
        return in_array($employeeRequest->status_id, [config('constant.APPROVED_STATUS')]);
    }
}
