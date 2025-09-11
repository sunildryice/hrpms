<?php

namespace Modules\GoodRequest\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Privilege\Repositories\UserRepository;

class AssignValidateController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected GoodRequestRepository $goodRequests,
        protected InventoryItemRepository $inventoryItems,
        protected ItemRepository $items,
        protected UserRepository $users
    ) {}

    public function __invoke(Request $request)
    {
        $inputs = $request->validate([
            'assigned_inventory_item_id' => 'nullable|array',
            'assigned_quantity' => 'nullable|array',
            'assigned_asset_ids' => 'nullable|array',
        ]);

        foreach ($inputs['assigned_quantity'] as $key => $quantity) {
            if ($quantity === null) {
                continue;
            }
            $inventoryItem = $this->inventoryItems->find($inputs['assigned_inventory_item_id'][$key]);
            if ($inventoryItem->getAvailableQuantity() < $quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Assigned quantity can not be greater than available quantity for item: '.$inventoryItem->item_name,
                ], 403);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Good request validated successfully.',
        ], 200);
    }
}
