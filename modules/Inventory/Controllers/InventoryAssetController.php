<?php

namespace Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Inventory\Requests\Asset\UpdateRequest;
use Modules\Master\Repositories\BrandRepository;

class InventoryAssetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AssetRepository $assets
     * @param InventoryItemRepository $inventoryItems
     * @param BrandRepository $brandsepository
     */
    public function __construct(
        AssetRepository $assets,
        InventoryItemRepository $inventoryItems,
        BrandRepository $brands
    )
    {
        $this->assets = $assets;
        $this->inventoryItems = $inventoryItems;
        $this->brands = $brands;
        $this->destinationPath = 'inventory';
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $inventoryItemId)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');
        $inventoryItem = $this->inventoryItems->find($inventoryItemId);

        if ($request->ajax()) {
            $data = $this->assets->select(['*'])
                ->where('inventory_item_id', $inventoryItemId);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('purchase_date', function ($row) use ($inventoryItem) {
                    return $inventoryItem->getPurchaseDate();
                })->addColumn('item_name', function ($row) use ($inventoryItem) {
                    return $inventoryItem->getItemName();
                })->addColumn('brand_name', function ($row) {
                    return $row->getBrandName();
                })->addColumn('asset_number', function ($row) {
                    return $row->getAssetNumber();
                })->addColumn('assigned_user', function ($row) {
                    return $row->getAssignedUserName();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('assets.show', $row->id) . '" rel="tooltip" title="View Detail"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('edit', $row)) {
                        $btn .= '&emsp;<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-asset-edit-modal-form" href="';
                        $btn .= route('inventories.assets.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }
    }

    public function edit($assetId)
    {
        $asset = $this->assets->find($assetId);
        $brands = $this->brands->getBrands();

        return view('Inventory::Asset.edit')
        ->withAsset($asset)
        ->withBrands($brands);
    }

    public function update(UpdateRequest $request, $assetId)
    {
        $inputs = $request->validated();
        $asset = $this->assets->update($assetId, $inputs);
        if ($asset) {
            return response()->json([
                'status' => 'ok',
                'asset' => $asset,
                'message' => 'Asset updated successfully.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Asset could not be updated.'
        ], 422);
    }

}
