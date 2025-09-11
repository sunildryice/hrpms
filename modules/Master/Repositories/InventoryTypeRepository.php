<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\InventoryType;

class InventoryTypeRepository extends Repository
{
    public function __construct(InventoryType $inventoryType)
    {
        $this->model = $inventoryType;
    }
}
