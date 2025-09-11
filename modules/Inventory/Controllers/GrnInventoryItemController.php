<?php

namespace Modules\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DataTables;

use Illuminate\Support\Facades\Storage;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistributionTypeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Supplier\Repositories\SupplierRepository;

use Modules\Inventory\Requests\StoreRequest;
use Modules\Inventory\Requests\UpdateRequest;

class GrnInventoryItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param GrnRepository $grns
     * @param InventoryItemRepository $inventoryItems
     * @param ItemRepository $items
     * @param SupplierRepository $suppliers
     */
    public function __construct(
        ActivityCodeRepository $activityCodes,
        DistributionTypeRepository $distributionTypes,
        DonorCodeRepository $donorCodes,
        GrnRepository $grns,
        InventoryItemRepository $inventoryItems,
        ItemRepository $items,
        SupplierRepository $suppliers
    )
    {
        $this->activityCodes = $activityCodes;
        $this->distributionTypes = $distributionTypes;
        $this->donorCodes = $donorCodes;
        $this->grns = $grns;
        $this->inventoryItems = $inventoryItems;
        $this->items = $items;
        $this->suppliers = $suppliers;
        $this->destinationPath = 'inventory';
    }

    /**
     * Show the form for creating a new purchase request by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $authUser = auth()->user();
        $this->authorize('inventory');
        $grns = $this->grns->select(['id','prefix', 'grn_number'])
            ->whereStatusid(config('constant.APPROVED_STATUS'))->get();

        return view('Inventory::grn.create')
            ->withGrns($grns);
    }

    /**
     * Store a newly created purchase request in storage.
     *
     * @param \Modules\PurchaseRequest\Requests\StoreRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $this->authorize('inventory');
        $inputs = $request->validated();
        $distributionType = $this->distributionTypes->find($request->distribution_type_id);
        $item = $this->items->find($request->item_id);
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $inputs['created_by'] = auth()->id();
        $inputs['category_id'] = $item->inventory_category_id;
        $inputs['item_name'] = $item->title;
        $inputs['asset_flag'] = $item->category->getInventoryType() != 'Consumable' && $distributionType->title != 'Distribution';;
        $inventoryItem = $this->inventoryItems->create($inputs);

        if ($inventoryItem) {
            return redirect()->route('inventories.index')
                ->withSuccessMessage('Inventory successfully added.');
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Inventory can not be added.');
    }
}
