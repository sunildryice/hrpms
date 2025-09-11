<?php

namespace Modules\MaintenanceRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;
use Modules\Privilege\Models\User;

class MaintenanceRequestPolicy
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
     * Determine if the given maintenance request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest  $maintenanceRequest
     * @return bool
     */
    public function approve(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return in_array($maintenanceRequest->status_id, [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')]) &&
            $user->id == $maintenanceRequest->approver_id;
    }

    /**
     * Determine if the given Maintenance request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest  $maintenanceRequest
     * @return bool
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return in_array($maintenanceRequest->status_id, [1, 2]) && in_array($user->id, [$maintenanceRequest->created_by, $maintenanceRequest->requester_id]);
    }

    /**
     * Determine if the given Maintenance request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest $travelRequest
     * @return bool
     */
    public function print(User $user, MaintenanceRequest $maintenanceRequest)
    {
         return in_array($maintenanceRequest->status_id, [config('constant.APPROVED_STATUS')]);
    }

    /**
     * Determine if the given maintenance request can be reviewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest  $maintenanceRequest
     * @return bool
     */
    public function review(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return ($maintenanceRequest->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $maintenanceRequest->reviewer_id);
    }

     /**
     * Determine if the given Maintenance request can be submitted by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest  $maintenanceRequest
     * @return bool
     */
    public function submit(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return in_array($maintenanceRequest->status_id, [1, 2]) && in_array($user->id, [$maintenanceRequest->created_by, $maintenanceRequest->requester_id]);
    }

    /**
     * Determine if the given Maintenance request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest $travelRequest
     * @return bool
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return in_array($maintenanceRequest->status_id, [1, 2]) && in_array($user->id, [ $maintenanceRequest->created_by, $maintenanceRequest->requester_id]);
    }

    /**
     * Determine if the given Maintenance request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\MaintenanceRequest\Models\MaintenanceRequest $travelRequest
     * @return bool
     */
    public function show(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return true;
    }

    public function amend(User $user, MaintenanceRequest $maintenanceRequest)
    {
        return in_array($maintenanceRequest->status_id, [config('constant.APPROVED_STATUS')]) && in_array($user->id, [$maintenanceRequest->created_by, $maintenanceRequest->requester_id]);
    }

}
