<?php

namespace Modules\TravelRequest\Policies;

use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\TravelRequest\Models\TravelReport;
use Modules\Privilege\Models\User;

class TravelReportPolicy
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
     * Determine if the given travel report can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @return bool
     */
    public function approve(User $user, TravelReport $travelReport)
    {
        return ($travelReport->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $travelReport->approver_id);
    }

    /**
     * Determine if the given travel report can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @return bool
     */
    public function delete(User $user, TravelReport $travelReport)
    {
        return in_array($travelReport->status_id, [config('constant.CREATED_STATUS')]) && in_array($user->id, [$travelReport->created_by]);
    }

    /**
     * Determine if the given travel report can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\TravelReport $travelReport
     * @return bool
     */
    public function print(User $user, TravelReport $travelReport)
    {
        // return ($travelReport->status_id == config('constant.APPROVED_STATUS') && in_array($user->id, [$travelReport->created_by, $travelReport->approver_id]));
        return (in_array($travelReport->status_id, [config('constant.APPROVED_STATUS')]));
    }

     /**
     * Determine if the given travel report can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\TravelRequest  $travelRequest
     * @param  \Modules\TravelRequest\Models\TravelRequestItinerary $travelRequestItinerary
     * @return bool
     */
    public function submit(User $user, TravelReport $travelReport)
    {
        return in_array($travelReport->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
        in_array($user->id, [$travelReport->created_by]) 
        &&
        ($travelReport->travelRequest->return_date->endOfDay()->isPast() || $travelReport->travelRequest->return_date->endOfDay()->isSameDay(now()));
    }

    /**
     * Determine if the given travel report can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\TravelReport $travelRequest
     * @return bool
     */
    public function update(User $user, TravelReport $travelReport)
    {
        return in_array($travelReport->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
        in_array($user->id, [ $travelReport->created_by]);
    }

    /**
     * Determine if the given travel report can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\TravelRequest\Models\TravelReport $travelReport
     * @return bool
     */
    public function view(User $user, TravelReport $travelReport)
    {
        return in_array($user->id, [$travelReport->created_by]) || $travelReport->status_id == config('constant.APPROVED_STATUS');
    }
}
