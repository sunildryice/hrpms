<?php

namespace Modules\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Inventory\Models\InventoryItem;
use Modules\Privilege\Models\User;

class InventoryItemPolicy
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
     * Determine if the given inventory item can be deleted by the user.
     *
     * @return bool
     */
    public function delete(User $user, InventoryItem $inventoryItem)
    {
        return in_array($user->id, [$inventoryItem->created_by]) &&
            ($inventoryItem->assigned_quantity == 0 || $inventoryItem->hasDuplicateAssets());
    }

    public function update(User $user, InventoryItem $inventoryItem)
    {
        return $user->can('manage-inventory') || $user->can('manage-inventory-finance');
    }

    public function updatePrice(User $user, InventoryItem $inventoryItem)
    {
        return $user->can('manage-inventory-finance') && ! isset($inventoryItem->grn_id) && ! isset($inventoryItem->grn_item_id);
    }
}
