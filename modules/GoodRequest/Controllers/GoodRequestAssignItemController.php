<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\GoodRequest\Repositories\GoodRequestItemRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;

use Modules\GoodRequest\Requests\Item\Assign\StoreRequest;

use DataTables;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;

class GoodRequestAssignItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param GoodRequestRepository $goodRequests
     * @param GoodRequestItemRepository $goodRequestItems
     * @param DonorCodeRepository $donorCodes
     * @param UnitRepository $units
     */
    public function __construct(
        ActivityCodeRepository    $activityCodes,
        GoodRequestRepository     $goodRequests,
        GoodRequestItemRepository $goodRequestItems,
        DonorCodeRepository       $donorCodes,
        InventoryItemRepository   $inventoryItems,
        ItemRepository            $items,
        UnitRepository            $units
    )
    {
        $this->activityCodes = $activityCodes;
        $this->goodRequests = $goodRequests;
        $this->goodRequestItems = $goodRequestItems;
        $this->donorCodes = $donorCodes;
        $this->inventoryItems = $inventoryItems;
        $this->items = $items;
        $this->units = $units;
    }

    /**
     * Show the form for creating a new good request item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($goodRequestId, $id)
    {
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('approve', $goodRequest);
        $goodRequestItem = $this->goodRequestItems->find($id);

        $inventoryItems = $this->inventoryItems->select(['id', 'assigned_quantity', 'quantity'])
            ->where('distribution_type_id', 1)
            ->get();
        $inventoryItemIds = $inventoryItems->filter(function ($inventoryItem){
            return $inventoryItem->quantity > $inventoryItem->assigned_quantity;
        })->pluck('id')->toArray();

        $inventoryItems = $this->inventoryItems->with('item')
            ->whereIn('id', $inventoryItemIds)->get();

        return view('GoodRequest::Approve.AssignItem.create')
            ->withGoodRequest($goodRequest)
            ->withGoodRequestItem($goodRequestItem)
            ->withInventoryItems($inventoryItems);
    }

    /**
     * Store a newly created good request item in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $goodRequestItem = $this->goodRequestItems->find($id);
        $inputs = $request->validated();
        $inventoryItem = $this->inventoryItems->find($request->assigned_inventory_item_id);
        $inputs['assigned_item_id'] = $inventoryItem->item_id;
        $inputs['assigned_unit_id'] = $inventoryItem->unit_id;
        $inputs['inventory_category_id'] = $inventoryItem->category_id;
        $goodRequestItem = $this->goodRequestItems->update($id, $inputs);

        if ($goodRequestItem) {
            return response()->json(['status' => 'ok',
                'goodRequest' => $goodRequestItem->goodRequest,
                'goodRequestItem' => $goodRequestItem,
                'message' => 'Good request item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Good request item can not be updated.'], 422);
    }
}
