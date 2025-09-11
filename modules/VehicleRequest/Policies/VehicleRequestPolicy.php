<?php

namespace Modules\VehicleRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\VehicleRequest\Models\VehicleRequest;
use Modules\Privilege\Models\User;

class VehicleRequestPolicy
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
     * Determine if the given vehicle request can be amended by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest  $vehicleRequest
     * @return bool
     */
    public function amend(User $user, VehicleRequest $vehicleRequest)
    {
        return in_array($vehicleRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')])
            && !$vehicleRequest->childvehicleRequest
            && in_array($user->id, [$vehicleRequest->requester_id, $vehicleRequest->created_by]);
    }

    /**
     * Determine if the given vehicle request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest  $vehicleRequest
     * @return bool
     */
    public function assignVehicle(User $user, VehicleRequest $vehicleRequest)
    {
        return $vehicleRequest->status_id == config('constant.SUBMITTED_STATUS') && 
                $vehicleRequest->approver_id == $user->id && 
                $vehicleRequest->vehicle_request_type_id == 1;
    }

    /**
     * Determine if the given vehicle request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest  $vehicleRequest
     * @return bool
     */
    public function approve(User $user, VehicleRequest $vehicleRequest)
    {
        return $user->id == $vehicleRequest->approver_id &&
                $vehicleRequest->vehicle_request_type_id == 2 &&
                in_array($vehicleRequest->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

    /**
     * Determine if the given vehicle request can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest  $vehicleRequest
     * @return bool
     */
    public function delete(User $user, VehicleRequest $vehicleRequest)
    {
        return in_array($vehicleRequest->status_id, [1, 2])
            && in_array($user->id, [$vehicleRequest->requester_id, $vehicleRequest->created_by]);
    }

    /**
     * Determine if the given vehicle request can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest $vehicleRequest
     * @return bool
     */
    public function print(User $user, VehicleRequest $vehicleRequest)
    {
        return (in_array($vehicleRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]) &&
                ($vehicleRequest->vehicle_request_type_id == 2) &&
                in_array($user->id, [$vehicleRequest->created_by, $vehicleRequest->approver_id]));
    }

    /**
     * Determine if the given vehicle request can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\VehicleRequest\Models\VehicleRequest  $vehicleRequest
     * @return bool
     */
    public function update(User $user, VehicleRequest $vehicleRequest)
    {
        return in_array($vehicleRequest->status_id, [1, 2]) && in_array($user->id, [$vehicleRequest->requester_id, $vehicleRequest->created_by]);
    }

    /**
     * Determine if the given vehicle request can be closed by the user.
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\VehicleRequest\Models\VehicleRequest $vehicleRequest
     * @return bool
     */
    public function close(User $user, VehicleRequest $vehicleRequest)
    {
        return $vehicleRequest->status_id == config('constant.APPROVED_STATUS') &&
            is_null($vehicleRequest->closed_at) &&
            in_array($user->id, $vehicleRequest->procurementOfficers->pluck('id')->toArray()) &&
            $vehicleRequest->vehicle_request_type_id == 2;
            
    }
    
}
