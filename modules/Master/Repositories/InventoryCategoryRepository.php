<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\InventoryCategory;

class InventoryCategoryRepository extends Repository
{
    public function __construct(InventoryCategory $inventoryCategory)
    {
        $this->model = $inventoryCategory;
    }
}
