<?php

namespace Modules\AssetDisposition\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\AssetDisposition\Models\DispositionRequest;
use Modules\Privilege\Models\User;

class DispositionRequestPolicy
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

    public function approve(User $user, DispositionRequest $dispositionRequest)
    {
        return $user->id == $dispositionRequest->approver_id &&
            in_array($dispositionRequest->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]);
    }

    /**
     * Determine if the given Asset Disposition cancelled by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AssetDisposition\Models\DispositionRequest  $dispositionRequest
     * @return bool
     */
    public function cancel(User $user, DispositionRequest $dispositionRequest)
    {
        return in_array($dispositionRequest->status_id, [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')]) &&
            in_array($user->id, [$dispositionRequest->requester_id, $dispositionRequest->created_by]);
            
    }


    /**
     * Determine if the given asset disposition report can be deleted by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AssetDisposition\Models\DispositionRequest $dispositionRequest
     * @return bool
     */
    public function delete(User $user, DispositionRequest $dispositionRequest)
    {
        return in_array($dispositionRequest->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) && 
            in_array($user->id, [$dispositionRequest->requester_id, $dispositionRequest->created_by]);
    }

    /**
     * Determine if the given asset disposition can be printed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\AssetDisposition\Models\DispositionRequest $dispositionRequest
     * @return bool
     */
    public function print(User $user, DispositionRequest $dispositionRequest)
    {
        return in_array($dispositionRequest->status_id, [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
    }


    /**
     * Determine if the given disposition request can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AssetDisposition\Models\DispositionRequest $dispositionRequest
     * @return bool
     */
    public function update(User $user, DispositionRequest $dispositionRequest)
    {
        return in_array($dispositionRequest->status_id, [1, 2]) && in_array($user->id, [$dispositionRequest->requester_id, $dispositionRequest->created_by]);
    }

    /**
     * Determine if the given asset disposition can be updated by the user.
     *
     * @param \Modules\Privilege\Models\User $user
     * @param \Modules\AssetDisposition\Models\DispositionRequest $dispositionRequest
     * @return bool
     */
    public function view(User $user, DispositionRequest $dispositionRequest)
    {
        return in_array($user->id, [$dispositionRequest->requester_id, $dispositionRequest->created_by]);
    }
}
