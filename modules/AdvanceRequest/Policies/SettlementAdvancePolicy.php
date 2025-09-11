<?php

namespace Modules\AdvanceRequest\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\AdvanceRequest\Models\Settlement;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettlementAdvancePolicy
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
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function approve(User $user, Settlement $settlement)
    {
        return $settlement->approver_id == $user->id && 
        in_array($settlement->status_id, [config('constant.VERIFIED_STATUS'), config('constant.VERIFIED2_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

    /**
     * Determine if the given approved settlement request can be viewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function viewApproved(User $user, Settlement $settlement)
    {
        return in_array($settlement->status_id, [config('constant.APPROVED_STATUS'),config('constant.PAID_STATUS')]);
    }

    /**
     * Determine if the given advance request can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function delete(User $user, Settlement $settlement)
    {
        return in_array($settlement->status_id, [1]) && in_array($user->id, [$settlement->requester_id, $settlement->created_by]);
    }

    /**
     * Determine if the given advance request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function print(User $user, Settlement $settlement)
    {
        return (($settlement->status_id == 6 || $settlement->status_id == 16) &&
            (in_array($user->id, [$settlement->created_by, $settlement->reviewer_id, $settlement->recommender_id, $settlement->approver_id])) ||
            $user->can('view-approved-advance-settlement'));
    }

    /**
     * Determine if the given travel claim can be reviewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function review(User $user, Settlement $settlement)
    {
        return ($settlement->status_id == config('constant.SUBMITTED_STATUS') && $settlement->reviewer_id == $user->id);
    }

    public function verify(User $user, Settlement $settlement)
    {
        return ($settlement->status_id == config('constant.VERIFIED_STATUS') && $settlement->verifier_id == $user->id);
    }

    /**
     * Determine if the given advance request  settlement can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AdvanceRequest\Models\Settlement  $settlement
     * @return bool
     */
    public function submit(User $user, Settlement $settlement)
    {
        return in_array($settlement->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$settlement->requester_id, $settlement->created_by]) &&
            $settlement->settlementExpenses->count();
    }

    /**
     * Determine if the given advance request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function update(User $user, Settlement $settlement)
    {
        return in_array($settlement->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
        in_array($user->id, [$settlement->created_by]);
    }

    /**
     * Determine if the given request can make payment confirmation by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AdvanceRequest\Models\Settlement $settlement
     * @return bool
     */
    public function pay(User $user, Settlement $settlement){
        return is_null($settlement->paid_at) &&  Gate::allows('pay-advance-settlement');
    }

    public function amend(User $user, Settlement $settlement)
    {
        return in_array($settlement->status_id, [config('constant.APPROVED_STATUS')]) && 
        in_array($user->id, [$settlement->requester_id, $settlement->created_by]);
    }
}
