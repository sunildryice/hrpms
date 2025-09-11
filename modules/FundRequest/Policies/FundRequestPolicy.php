<?php

namespace Modules\FundRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\FundRequest\Models\FundRequest;
use Modules\Privilege\Models\User;

class FundRequestPolicy
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

    public function check(User $user, FundRequest $fundRequest)
    {
        return $fundRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $fundRequest->checker_id;
    }

    public function certify(User $user, FundRequest $fundRequest)
    {
        return $fundRequest->status_id == config('constant.VERIFIED_STATUS') && $user->id == $fundRequest->certifier_id;
    }

    public function replicate(User $user, FundRequest $fundRequest)
    {
        return $fundRequest->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]);
    }

    /**
     * Determine if the given fund request can be reviewed by the user.
     *
     * @return bool
     */
    public function review(User $user, FundRequest $fundRequest)
    {
        return ($fundRequest->status_id == config('constant.VERIFIED2_STATUS') && $user->id == $fundRequest->reviewer_id) ||
            ($fundRequest->status_id == config('constant.SUBMITTED_STATUS') && !isset($fundRequest->certifier->id)
                && !isset($fundRequest->checker_id) && $user->id == $fundRequest->reviewer_id);
    }

    /**
     * Determine if the given fund request can be approved by the user.
     *
     * @return bool
     */
    public function approve(User $user, FundRequest $fundRequest)
    {
        return ($fundRequest->status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $fundRequest->approver_id) ||
        ($fundRequest->status_id == config('constant.VERIFIED3_STATUS') && $user->id == $fundRequest->approver_id);
    }

    /**
     * Determine if the given fund request can be deleted by the user.
     *
     * @return bool
     */
    public function delete(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]);
    }

    /**
     * Determine if the given fund request can be printed by the user.
     *
     * @return bool
     */
    public function print(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.CANCELLED_STATUS')]);
    }

    public function cancel(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
        in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]);
    }

    public function approveCancel(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.INIT_CANCEL_STATUS')]) &&
            $user->id == $fundRequest->approver_id;
    }

    /**
     * Determine if the given fund request can be submitted by the user.
     *
     * @return bool
     */
    public function submit(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]) &&
            $fundRequest->fundRequestActivities->count();
    }

    /**
     * Determine if the given fund request can be updated by the user.
     *
     * @return bool
     */
    public function update(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]);
    }

    public function updateActivity(User $user, FundRequest $fundRequest)
    {
        return (in_array($fundRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]))
            || (in_array($fundRequest->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.VERIFIED_STATUS')])
                && ($user->can('check-fund-request') || $user->can('certify-fund-request')));
    }

    /**
     * Determine if the given approved fund request can be viewed by the user.
     *
     * @return bool
     */
    public function viewApproved(User $user, FundRequest $fundRequest)
    {
        return in_array($fundRequest->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function amend(User $user, FundRequest $fundRequest)
    {
        return $fundRequest->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$fundRequest->requester_id, $fundRequest->created_by]);
    }

}
