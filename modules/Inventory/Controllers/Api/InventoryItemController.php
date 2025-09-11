<?php

namespace Modules\Inventory\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Inventory\Repositories\InventoryItemRepository;

class InventoryItemController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param InventoryItemRepository $inventoryItems
     */
    public function __construct(
        protected InventoryItemRepository $inventoryItems
    ) {
        $this->inventoryItems = $inventoryItems;
        $this->destinationPath = 'inventory';
    }

    /**
     * Show the specified inventory item.
     *
     * @param $inventoryId
     * @return mixed
     */
    public function show($inventoryId)
    {
        $authUser = auth()->user();
        $inventoryItem = $this->inventoryItems->find($inventoryId);
        $assets = $inventoryItem->assets()
            ->with('latestConditionLog')
            ->where(function ($q) {
                $q->whereIn('status', [config('constant.ASSET_NEW'), config('constant.ASSET_ON_STORE')])
                    ->orWhereHas('latestGoodRequestAsset', function ($q) {
                        $q->whereNull('assigned_user_id');
                    });
            })
            ->whereDoesntHave('dispositionRequest', function ($query) {
                $query->where('status_id', config('constant.APPROVED_STATUS'));
            })
            ->get();

        $inventoryTypeBool = $inventoryItem->category->inventoryType->title == 'Consumable';
        return response()->json([
            'assets' => $assets,
            'unit' => $inventoryItem->getUnitName(),
            'item' => $inventoryItem->item,
            'inventoryItem' => $inventoryItem,
            'availableQuantity' => $inventoryItem->getAvailableQuantity(),
            // 'availableQuantity' => $inventoryTypeBool ?$inventoryItem->getAvailableQuantity():$assets->count(),
            'consumable' => $inventoryTypeBool,
        ], 200);
    }

    public function disposable($officeId)
    {
        $authUser = auth()->user();
        $office = $this->offices->find($officeId);
        $assets = $this->model->whereDoesntHave('disposition', function ($query) {
            $query->where('status_id', config('constant.APPROVED_STATUS'));
        })->where('assigned_office_id', $office->id)
        ->get();
        return response()->json([
            'assets' => $assets,
        ], 200);
    }
}
