<?php

namespace Modules\ExitStaffClearance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\Privilege\Models\User;
use Modules\Privilege\Repositories\UserRepository;

class StaffClearancePolicy
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

    public function submit(User $user, StaffClearance $staffClearance)
    {
        return in_array($staffClearance->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$staffClearance->requester_id]);
    }

    public function verify(User $user, StaffClearance $staffClearance)
    {
        $tenure = $staffClearance->employee->latestTenure;
        $employeeId = $user->employee?->id;

        return ($tenure->supervisor_id == $employeeId || $tenure->cross_supervisor_id == $employeeId || $tenure->next_line_manager_id == $employeeId) && $staffClearance->status_id == config('constant.CREATED_STATUS');
    }

    public function certify(User $user, StaffClearance $staffClearance)
    {
        return $staffClearance->status_id == config('constant.VERIFIED_STATUS') && $user->can('hr-staff-clearance');
    }

    public function endorse(User $user, StaffClearance $staffClearance)
    {
        return $staffClearance->status_id == config('constant.VERIFIED2_STATUS') &&
        $user->id == $staffClearance->endorser_id;
    }

    public function edit(User $user, StaffClearance $staffClearance)
    {
        return in_array($staffClearance->status_id, [config('constant.CREATED_STATUS'), config('constant.VERIFIED_STATUS')]) && $staffClearance->employee->user->id != $user->id  ;
    }

    public function recommend(User $user, StaffClearance $staffClearance)
    {
        return $staffClearance->status_id == config('constant.VERIFIED_STATUS') &&
        $user->id == $staffClearance->recommender_id;
    }

    public function approve(User $user, StaffClearance $staffClearance)
    {
        return $staffClearance->status_id == config('constant.VERIFIED3_STATUS') &&
        $user->id == $staffClearance->approver_id;
    }

    public function delete(User $user, StaffClearance $staffClearance)
    {
        return (($staffClearance->status_id == config('constant.CREATED_STATUS')) &&
        ($user->id == $staffClearance->created_by)) &&
        $user->can('manage-performance-review');
    }

    public function print(User $user, StaffClearance $staffClearance)
    {
        return $staffClearance->status_id == config('constant.APPROVED_STATUS');
    }
}
