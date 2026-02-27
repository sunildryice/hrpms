<?php

namespace Modules\TravelRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Privilege\Models\User;
use Modules\TravelRequest\Models\TravelRequest;

class TravelRequestPolicy
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
     * @return bool
     */
    public function amend(User $user, TravelRequest $travelRequest)
    {
        return $travelRequest->status_id == config('constant.APPROVED_STATUS') &&
            !$travelRequest->travelClaim &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]);
    }

    /**
     * Determine if the given travel request can be approved by the user.
     *
     * @return bool
     */
    public function approve(User $user, TravelRequest $travelRequest)
    {
        return $user->id == $travelRequest->approver_id &&
            in_array($travelRequest->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

    /**
     * Determine if the given travel request can be cancelled by the user.
     *
     * @return bool
     */
    public function cancel(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by])
            && !$travelRequest->travelReport;
        // && (now() < $travelRequest->departure_date);
    }

    /**
     * Determine if the given travel request cancellation request can be approved.
     *
     * @return bool
     */
    public function approveCancel(User $user, TravelRequest $travelRequest)
    {
        return $user->id == $travelRequest->approver_id &&
            in_array($travelRequest->status_id, [config('constant.INIT_CANCEL_STATUS')]);
    }

    /**
     * Determine if the given travel request can be reported by the user.
     *
     * @return bool
     */
    public function createClaim(User $user, TravelRequest $travelRequest)
    {
        return
            $travelRequest->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]) &&
            $travelRequest->travelReport && !$travelRequest->travelClaim;

        //        return now() >= $travelRequest->return_date &&
        //            $travelRequest->status_id == config('constant.APPROVED_STATUS') &&
        //            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]) &&
        //            $travelRequest->travelReport && ! $travelRequest->travelClaim;
    }

    /**
     * Determine if the given travel request can be reported by the user.
     *
     * @return bool
     */
    public function createReport(User $user, TravelRequest $travelRequest)
    {
        return (now() > $travelRequest->departure_date && $travelRequest->status_id == config('constant.APPROVED_STATUS') &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by])) && !$travelRequest->travelReport;
    }

    /**
     * Determine if the given travel request can be deleted by the user.
     *
     * @return bool
     */
    public function delete(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]);
    }

    /**
     * Determine if the given travel request can be printed by the user.
     *
     * @return bool
     */
    public function print(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
    }

    /**
     * Determine if the given travel request can be submitted by the user.
     *
     * @param  \Modules\TravelRequest\Models\TravelRequestItinerary  $travelRequestItinerary
     * @return bool
     */
    public function submit(User $user, TravelRequest $travelRequest)
    {
        if (!in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]) || !in_array($travelRequest->status_id, [1, 2])) {
            return false;
        }
        if (!$travelRequest->departure_date || !$travelRequest->return_date) {
            return false;
        }
        $startDate = \Carbon\Carbon::parse($travelRequest->departure_date);
        $endDate = \Carbon\Carbon::parse($travelRequest->return_date);
        $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);
        $itineraries = $travelRequest->travelRequestDayItineraries;

        foreach ($period as $date){
            $exists = $itineraries->filter(function ($itinerary) use ($date) {
                return $itinerary->date->format('Y-m-d') == $date->format('Y-m-d');
            })->count();
            if ($exists == 0){
                return false;
            }
        }
        return true;
    }

    /**
     * Determine if the given travel request can be updated by the user.
     *
     * @return bool
     */
    public function update(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [1, 2]) && in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]);
    }

    /**
     * Determine if the given travel request can be updated by the user.
     *
     * @return bool
     */
    public function view(User $user, TravelRequest $travelRequest)
    {
        return in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]);
    }

    public function askAdvance(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
            in_array($user->id, [$travelRequest->requester_id, $travelRequest->created_by]) &&
            $travelRequest->advance_requested_at == null && $travelRequest->advance_received_at == null &&
            !$travelRequest->travelClaim;
    }

    public function giveAdvance(User $user, TravelRequest $travelRequest)
    {
        return in_array($travelRequest->status_id, [config('constant.APPROVED_STATUS')]) &&
            $travelRequest->advance_received_at == null &&
            $user->can('travel-request-advance') &&
            !$travelRequest->travelClaim;
    }
}
