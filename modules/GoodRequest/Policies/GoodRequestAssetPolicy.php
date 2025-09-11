<?php

namespace Modules\GoodRequest\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\GoodRequest\Models\GoodRequestAsset;
use Modules\Privilege\Models\User;

class GoodRequestAssetPolicy
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
     * Determine if the given good request can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\GoodRequest\Models\GoodRequestAsset  $goodRequestAsset
     * @return bool
     */
    public function approve(User $user, GoodRequestAsset $goodRequestAsset)
    {
        return ($goodRequestAsset->handover_status_id == config('constant.SUBMITTED_STATUS') && $user->id == $goodRequestAsset->reviewer_id)
        || ($goodRequestAsset->handover_status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $goodRequestAsset->approver_id);
    }

    /**
     * Determine if the given handovered good request asset can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\GoodRequest\Models\GoodRequestAsset  $goodRequestAssetAsset
     * @return bool
     */
    public function handover(User $user, GoodRequestAsset $goodRequestAsset)
    {
        return in_array($goodRequestAsset->status, [config('constant.ASSET_ASSIGNED')]) &&
            in_array($goodRequestAsset->handover_status_id, [0, config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]) &&
             $goodRequestAsset->asset->inventoryItem->category->inventoryType->title == 'Non Consumable';
    }
}
