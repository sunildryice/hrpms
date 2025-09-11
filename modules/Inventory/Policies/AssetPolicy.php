<?php

namespace Modules\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Grn\Models\Grn;
use Modules\Inventory\Models\Asset;
use Modules\Privilege\Models\User;

class AssetPolicy
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

    public function edit(User $user, Asset $asset)
    {
        return $user->can('manage-asset') && $asset;
    }

    public function directAssign(User $user, Asset $asset)
    {
        if(!$user->can('direct-assign-good-request')){
            return false;
        }

        if($asset->dispositionRequest?->status_id == config('constant.APPROVED_STATUS')){
            return false;
        }

        if($goodRequestAsset = $asset->goodRequestAsset()->latest()->first()){
            if($goodRequestAsset->goodRequest){
                if($goodRequestAsset->assigned_user_id == null){
                    return $goodRequestAsset->goodRequest?->status_id == config('constant.REJECTED_STATUS');
                }
                return $goodRequestAsset->handover_status_id == config('constant.APPROVED_STATUS');
            }
        }
        return !isset($asset->assigned_user_id);
    }

}
