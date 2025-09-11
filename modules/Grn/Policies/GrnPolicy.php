<?php

namespace Modules\Grn\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Grn\Models\Grn;
use Modules\Grn\Repositories\GrnItemRepository;
use Modules\Privilege\Models\User;

class GrnPolicy
{
    use HandlesAuthorization;

    private $grnItems;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct(GrnItemRepository $grnItems)
    {
        $this->grnItems = $grnItems;
    }

    /**
     * Determine if the given grn can be approved by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Grn\Models\Grn  $grn
     * @return bool
     */
    public function approve(User $user, Grn $grn)
    {
        return ($grn->status_id == config('constant.SUBMITTED_STATUS') && $user->id == $grn->reviewer_id) ||
            ($grn->status_id == config('constant.RECOMMENDED_STATUS') && $user->id == $grn->approver_id);
    }

    /**
     * Determine if the given grn can be deleted by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Grn\Models\Grn  $grn
     * @return bool
     */
    public function delete(User $user, Grn $grn)
    {
        return in_array($grn->status_id, [config('constant.CREATED_STATUS')]) && in_array($user->id, [$grn->created_by]);
    }

    /**
     * Determine if the given grn can be updated by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Grn\Models\Grn  $grn
     * @return bool
     */
    public function update(User $user, Grn $grn)
    {
        return in_array($grn->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
            //&& in_array($user->id, [$grn->created_by]);
    }

    /**
     * Determine if the given approved purchase order can be viewed by the user.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Grn\Models\Grn  $grn
     * @return bool
     */
    public function viewApproved(User $user, Grn $grn)
    {
        return in_array($grn->status_id, [config('constant.APPROVED_STATUS')]);
    }

    public function createBulkInventory(User $user, Grn $grn)
    {
        $grnItemsCount = $this->grnItems->where('grn_id', '=', $grn->id)->doesntHave('inventoryItem')->count();
        return $grnItemsCount > 0;
    }

    public function unreceive(User $user, Grn $grn)
    {
        $totalGrnItemsCount = $this->grnItems->where('grn_id', '=', $grn->id)->count();
        $grnItemsWithoutInventoryCount = $this->grnItems->where('grn_id', '=', $grn->id)->doesntHave('inventoryItem')->count();
        return $grnItemsWithoutInventoryCount == $totalGrnItemsCount && in_array($grn->status_id, [config('constant.APPROVED_STATUS')]);
    }
}
