<?php

namespace Modules\TrainingRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Privilege\Models\User;
use Modules\TrainingRequest\Models\TrainingReport;

class TrainingReportPolicy
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
     * Determine if the given training report can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport  $trainingReport
     * @return bool
     */
    public function approve(User $user, TrainingReport $trainingReport)
    {
        return (in_array($trainingReport->status_id, [3, 4]) && in_array($user->id, [$trainingReport->approver_id, $trainingReport->reviewer_id]));
    }

    /**
     * Determine if the given training report can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport  $trainingReport
     * @return bool
     */
    public function delete(User $user, TrainingReport $trainingReport)
    {
        return in_array($trainingReport->status_id, [1, 2]) && in_array($user->id, [$trainingReport->created_by]);
    }

    /**
     * Determine if the given training request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function print(User $user, TrainingReport $trainingReport)
    {
        return $trainingReport->status_id == config('constant.APPROVED_STATUS') && $user->can('view-approved-training-report');
        //     &&
        //     (
        //     in_array($user->id, [$trainingReport->created_by, $trainingReport->reviewer_id, $trainingReport->approver_id])
        //     ||
        //     $user->hasRole('Human Resource')
        // );
    }

    /**
     * Determine if the given training request can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport  $trainingReport
     * @return bool
     */
    public function submit(User $user, TrainingReport $trainingReport)
    {
        return in_array($trainingReport->status_id, [1, 2]) && in_array($user->id, [$trainingReport->created_by]);
    }

    /**
     * Determine if the given training request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport $trainingReport
     * @return bool
     */
    public function update(User $user, TrainingReport $trainingReport)
    {
        return in_array($trainingReport->status_id, [1, 2]) && in_array($user->id, [$trainingReport->created_by]);
    }

    /**
     * Determine if the given training request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport $trainingReport
     * @return bool
     */
    public function view(User $user, TrainingReport $trainingReport)
    {
        return in_array($user->id, [$trainingReport->created_by]);
    }

    /**
     * Determine if the given monthly work log can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingReport $trainingReport
     * @return bool
     */
    public function viewApproved(User $user, TrainingReport $trainingReport)
    {
        return in_array($trainingReport->status_id, [6]) && $user->can('view-approved-training-report');
        // &&
        //     in_array($user->id, [$trainingReport->created_by, $trainingReport->reviewer_id, $trainingReport->approver_id]);
    }

}
