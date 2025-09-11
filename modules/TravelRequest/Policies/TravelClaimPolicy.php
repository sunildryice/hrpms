<?php

namespace Modules\TravelRequest\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\TravelRequest\Models\TravelClaim;
use Illuminate\Auth\Access\HandlesAuthorization;

class TravelClaimPolicy
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
     * Determine if the given travel claim can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function approve(User $user, TravelClaim $travelClaim)
    {
        return ($travelClaim->status_id == config('constant.VERIFIED_STATUS') && $user->id == $travelClaim->recommender_id) ||
            (in_array($travelClaim->status_id, [config('constant.RECOMMENDED_STATUS'), config('constant.RECOMMENDED2_STATUS')]) &&
                $user->id == $travelClaim->approver_id);
    }

    /**
     * Determine if the given travel claim can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function delete(User $user, TravelClaim $travelClaim)
    {
        return in_array($travelClaim->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            && in_array($user->id, [$travelClaim->created_by]);
    }

    /**
     * Determine if the given travel claim can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function print(User $user, TravelClaim $travelClaim)
    {
        return ($travelClaim->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$travelClaim->created_by, $travelClaim->reviewer_id, $travelClaim->approver_id]));
    }

    public function printApproved(User $user, TravelClaim $travelClaim)
    {
        return ($travelClaim->status_id == config('constant.APPROVED_STATUS') || $travelClaim->status_id == config('constant.PAID_STATUS'));
    }

    /**
     * Determine if the given travel claim can be reviewed by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function review(User $user, TravelClaim $travelClaim)
    {
        return ($travelClaim->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $travelClaim->reviewer_id);
    }

    /**
     * Determine if the given travel claim can be submitted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelRequest $travelClaim
     * @return bool
     */
    public function submit(User $user, TravelClaim $travelClaim)
    {
        return in_array($travelClaim->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$travelClaim->created_by]) &&
            $travelClaim->travelRequest->travelReport->status_id == config('constant.APPROVED_STATUS');
    }

    /**
     * Determine if the given travel claim can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function update(User $user, TravelClaim $travelClaim)
    {
        return in_array($travelClaim->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$travelClaim->created_by]);
    }

    /**
     * Determine if the given travel claim can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelRequest\Models\TravelClaim $travelClaim
     * @return bool
     */
    public function view(User $user, TravelClaim $travelClaim)
    {
        return in_array($user->id, [$travelClaim->created_by]);
    }

    public function pay(User $user, TravelClaim $travelClaim){
        return is_null($travelClaim->paid_at) &&  Gate::allows('pay-travel-claim');
    }
}
