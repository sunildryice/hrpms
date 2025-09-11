<?php

namespace Modules\Master\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\ItemRepository;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  AssetRepository $assets
     * @param  ItemRepository $items
     * @return void
     */
    public function __construct(
        AssetRepository $assets,
        ItemRepository $items
    )
    {
        $this->assets = $assets;
        $this->items = $items;
    }

    /**
     * Display a listing of the province.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'items'=>$this->items->get()
        ], 200);
    }

    /**
     * Display the specified province.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = $this->items->find($id);
        $inventoryItemIds = $item->inventoryItems->pluck('id')->toArray();
        $assets = $this->assets->select(['*'])
            ->whereIn('inventory_item_id', $inventoryItemIds)
            ->whereIn('status', [config('constant.ASSET_NEW'), config('constant.ASSET_ON_STORE')])
            ->get();
        return response()->json([
            'assets'=>$assets,
            'category'=>$item->category,
            'inventoryType'=>$item->category->inventoryType,
            'consumable'=>$item->category->inventoryType->title == 'Consumable',
            'units'=>$item->units->whereNotNull('activated_at'),
            'item'=>$item,
        ], 200);
    }
}
