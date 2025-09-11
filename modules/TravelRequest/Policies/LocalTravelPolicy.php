<?php

namespace Modules\TravelRequest\Policies;

use Modules\Privilege\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\TravelRequest\Models\LocalTravel;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocalTravelPolicy
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
     * Determine if the given local travel can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\LocalTravel  $localTravel
     * @return bool
     */
    public function approve(User $user, LocalTravel $localTravel)
    {
        return in_array($localTravel->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                && $user->id == $localTravel->approver_id;
    }

    /**
     * Determine if the given local travel can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\LocalTravel  $localTravel
     * @return bool
     */
    public function delete(User $user, LocalTravel $localTravel)
    {
        return in_array($localTravel->status_id, [config('constant.CREATED_STATUS')]) &&
            in_array($user->id, [$localTravel->requester_id, $localTravel->created_by]);
    }

    /**
     * Determine if the given local travel can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\LocalTravel $localTravel
     * @return bool
     */
    public function print(User $user, LocalTravel $localTravel)
    {
        return in_array($localTravel->status_id,[config('constant.APPROVED_STATUS'),config('constant.PAID_STATUS')]);
    }

     /**
     * Determine if the given local travel can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\LocalTravel  $localTravel
     * @param  \Modules\TravelRequest\Models\TravelRequestItinerary $localTravelItinerary
     * @return bool
     */
    public function submit(User $user, LocalTravel $localTravel)
    {
        return $localTravel->travelRequestItinerary->count() != 0 &&
            in_array($localTravel->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$localTravel->requester_id, $localTravel->created_by]);
    }

    /**
     * Determine if the given local travel can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\LocalTravel $localTravel
     * @return bool
     */
    public function update(User $user, LocalTravel $localTravel)
    {
        return in_array($localTravel->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$localTravel->requester_id, $localTravel->created_by]);
    }

    /**
     * Determine if the given local travel can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\LocalTravel $localTravel
     * @return bool
     */
    public function view(User $user, LocalTravel $localTravel)
    {
        return in_array($user->id, [$localTravel->requester_id, $localTravel->created_by]);
    }

    public function pay(User $user, LocalTravel $localTravel){
        return is_null($localTravel->paid_at) &&  Gate::allows('pay-local-travel');
    }
}
