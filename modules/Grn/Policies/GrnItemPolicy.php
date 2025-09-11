<?php

namespace Modules\Grn\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Grn\Models\GrnItem;
use Modules\Privilege\Models\User;

class GrnItemPolicy
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
     * Determine if the inventory can be created by the user for grn item.
     *
     * @param  \Modules\Privilege\Models\User  $user
     * @param  \Modules\Grn\Models\GrnItem  $grnItem
     * @return bool
     */
    public function createInventory(User $user, GrnItem $grnItem)
    {
        return $user->can('manage-inventory') && !$grnItem->inventoryItem()->exists();
    }
}
