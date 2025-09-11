<?php

namespace Modules\TrainingRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\TrainingRequest\Models\TrainingRequest;
use Modules\Privilege\Models\User;

class TrainingRequestPolicy
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
     * Determine if the given training request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function approve(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [config('constant.RECOMMENDED2_STATUS')]) && in_array($user->id, [$trainingRequest->approver_id]);
    }

    /**
     * Determine if the given training report can be created by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function createReport(User $user, TrainingRequest $trainingRequest)
    {
        $currentDate = date('Y-m-d');
        $today = date('Y-m-d', strtotime($currentDate));
        $training_start_date = date('Y-m-d', strtotime($trainingRequest->start_date));
        $valid_till = date('Y-m-d', strtotime($training_start_date.'+7 days'));
        return ($today >= $training_start_date && $trainingRequest->status_id == 6 && in_array($user->id, [$trainingRequest->created_by]));
        // return ($today > $training_start_date && $today <= $valid_till && $trainingRequest->status_id == 6 && in_array($user->id, [$trainingRequest->created_by]) && $trainingRequest->trainingReport->count() > 0);
    }

    /**
     * Determine if the given training request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest $trainingRequest
     * @return bool
     */
    public function delete(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [1]) && in_array($user->id, [ $trainingRequest->created_by]);
    }

    /**
     * Determine if the given training request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function print(User $user, TrainingRequest $trainingRequest)
    {
        return $trainingRequest->status_id == config('constant.APPROVED_STATUS');
    }

    /**
     * Determine if the given training request can be recommended by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function recommend(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [config('constant.RECOMMENDED_STATUS')]) && 
        in_array($user->id, [$trainingRequest->recommender_id]);
    }

    /**
     * Determine if the given training request can be reviewed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function review(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [3]) && in_array($user->id, [$trainingRequest->reviewer_id]);
    }

    /**
     * Determine if the given monthly work log can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $trainingRequest
     * @return bool
     */
    public function update(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [1, 2]) && in_array($user->id, [ $trainingRequest->created_by]);
    }

    /**
     * Determine if the given monthly work log can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $TrainingRequest
     * @return bool
     */
    public function view(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [3,2,4,5,6,7,8]) && in_array($user->id, [ $trainingRequest->created_by, $trainingRequest->reviewer_id, $trainingRequest->recommender_id, $trainingRequest->approver_id]);
    }

    /**
     * Determine if the given training request be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TrainingRequest\Models\TrainingRequest  $TrainingRequest
     * @return bool
     */
    public function viewApproved(User $user, TrainingRequest $trainingRequest)
    {
        // return true;
        return in_array($trainingRequest->status_id, [6]); 
        // && in_array($user->id, [ $trainingRequest->created_by, $trainingRequest->reviewer_id, $trainingRequest->recommender_id, $trainingRequest->approver_id]);
    }
}
