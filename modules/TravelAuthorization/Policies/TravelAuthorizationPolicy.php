<?php

namespace Modules\TravelAuthorization\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\TravelAuthorization\Models\TravelAuthorization;
use Modules\TravelAuthorization\Models\TravelAuthorizationItinerary;

class TravelAuthorizationPolicy
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
     * Determine if the given travel request can be amended by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelAuthorization\Models\TravelAuthorization  $travel
     * @return bool
     */
    public function amend(User $user, TravelAuthorization $travel)
    {
        return $travel->status_id == config('constant.APPROVED_STATUS') &&
            !$travel->travelClaim &&
            in_array($user->id, [$travel->requester_id, $travel->created_by]);
    }

    /**
     * Determine if the given travel request can be approved by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelAuthorization\Models\TravelAuthorization $travel
     * @return bool
     */
    public function approve(User $user, TravelAuthorization $travel)
    {
        return $user->id == $travel->approver_id &&
            in_array($travel->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }


    /**
     * Determine if the given travel request can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelAuthorization\Models\TravelAuthorization $travel
     * @return bool
     */
    public function delete(User $user, TravelAuthorization $travel)
    {
        return in_array($travel->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$travel->requester_id, $travel->created_by]);
    }

    /**
     * Determine if the given travel request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelAuthorization\Models\TravelAuthorization $travel
     * @return bool
     */
    public function print(User $user, TravelAuthorization $travel)
    {
        return in_array($travel->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
    }


    public function submit(User $user, TravelAuthorization $travel)
    {
        return $travel->itineraries->count() > 0 && $travel->officials()->count() > 0 && $travel->estimates()->count() > 0 && in_array($travel->status_id, [1, 2]) && in_array($user->id, [$travel->requester_id, $travel->created_by]);
    }

    /**
     * Determine if the given travel request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelAuthorization\Models\TravelAuthorization $travel
     * @return bool
     */
    public function update(User $user, TravelAuthorization $travel)
    {
        return in_array($travel->status_id, [1, 2]) && in_array($user->id, [$travel->requester_id, $travel->created_by]);
    }

    /**
     * Determine if the given travel request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\TravelAuthorization\Models\TravelAuthorization $travel
     * @return bool
     */
    public function view(User $user, TravelAuthorization $travel)
    {
        return in_array($user->id, [$travel->requester_id, $travel->created_by]);
    }
}
