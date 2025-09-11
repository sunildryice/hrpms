<?php

namespace Modules\Inventory\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Inventory\Repositories\AssetRepository;

class AssetController extends Controller
{
    private $assets;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        AssetRepository $assets,
    ) {
        $this->assets = $assets;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');

        if ($request->ajax()) {
            $data = $this->assets->with([
                'assignedTo',
                'assignedTo',
                'assignedTo.employee.latestTenure.office',
                'inventoryItem',
                'inventoryItem.office',
                'latestConditionLog',
                'latestConditionLog.condition',
            ])
                ->select(['*'])
                ->whereDoesntHave('dispositionRequest', function ($query) {
                    $query->where('status_id', config('constant.APPROVED_STATUS'));
                })
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('purchase_date', function ($row) {
                    return $row->inventoryItem->getPurchaseDate();
                })->addColumn('item_name', function ($row) {
                    return $row->inventoryItem->getItemName();
                })->addColumn('assigned_user', function ($row) {
                    return $row->getAssignedUserName();
                })->addColumn('asset_condition', function ($row) {
                    return $row->getAssetCondition();
                })->addColumn('assigned_location', function ($row) {
                    return $row->getAssignedUserOfficeLocation();
                })->addColumn('specification', function ($row) {
                    return $row->getSpecification();
                })->addColumn('asset_number', function ($row) {
                    return $row->getAssetNumber();
                })->addColumn('serial_number', function ($row) {
                    return $row->getSerialNumber();
                })->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('assets.show', $row->id).'" rel="tooltip" title="View Detail"><i class="bi bi-eye"></i></a>';

                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('Inventory::Asset.index');
    }

    public function show(Request $request, $assetId)
    {
        $asset = $this->assets->find($assetId);
        $inventory = $asset->inventoryItem;
        $goodRequestAssets = $asset->goodRequestAsset;
        $assetConditionLogs = $asset->assetConditionLogs;
        $disposition = $asset->dispositionRequest;

        return view('Inventory::Asset.show')
            ->withAsset($asset)
            ->withAssetConditionLogs($assetConditionLogs)
            ->withInventory($inventory)
            ->withDisposition($disposition)
            ->withGoodRequestAssets($goodRequestAssets);
    }
}
