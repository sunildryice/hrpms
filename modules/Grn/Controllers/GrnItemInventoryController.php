<?php

namespace Modules\Grn\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Grn\Notifications\InventoryPushed;
use Modules\Grn\Notifications\InventoryPushedBulk;
use Modules\Grn\Repositories\GrnItemRepository;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Grn\Requests\Inventory\StoreRequest;
use Modules\Master\Repositories\DistributionTypeRepository;
use Modules\Master\Repositories\ExecutionRepository;
use Modules\Privilege\Repositories\UserRepository;

class GrnItemInventoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected DistributionTypeRepository $distributionTypes,
        protected ExecutionRepository $executions,
        protected GrnRepository $grns,
        protected GrnItemRepository $grnItems,
        protected UserRepository $users
    ) {}

    public function create($grnId, $id)
    {
        $grnItem = $this->grnItems->find($id);
        $this->authorize('createInventory', $grnItem);

        $distributionTypes = $this->distributionTypes->get();
        $executionTypes = $this->executions->getExecutions();
        $interpreters = $this->users->permissionBasedUsers('manage-inventory-finance')->merge($this->users->permissionBasedUsers('manage-asset-logistic'));

        return view('Grn::Inventory.create')->with([
            'distributionTypes' => ($distributionTypes),
            'executionTypes' => ($executionTypes),
            'grnItem' => ($grnItem),
            'interpreters' => ($interpreters),
        ]);
    }

    public function createBulk($grnId)
    {
        $grn = $this->grns->find($grnId);
        $distributionTypes = $this->distributionTypes->get();
        $executionTypes = $this->executions->getExecutions();
        $interpreters = $this->users->permissionBasedUsers('manage-inventory-finance')->merge($this->users->permissionBasedUsers('manage-asset-logistic'));

        return view('Grn::Inventory.Bulk.create')->with([
            'distributionTypes' => ($distributionTypes),
            'executionTypes' => ($executionTypes),
            'grn' => ($grn),
            'interpreters' => ($interpreters),
        ]);
    }

    /**
     * Update the specified grn in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $grnId, $id)
    {
        $grn = $this->grns->find($grnId);
        $grnItem = $this->grnItems->find($id);
        $this->authorize('createInventory', $grnItem);
        $distributionType = $this->distributionTypes->find($request->distribution_type_id)?->title;

        $inputs = [
            'office_id' => $grn->office_id,
            'distribution_type_id' => $request->distribution_type_id,
            'execution_id' => $request->execution_id,
            'supplier_id' => $grn->supplier_id,
            // 'purchase_date'=>$grn->purchaseOrder->order_date ? $grn->purchaseOrder->order_date->format('Y-m-d') : $request->order_date,
            // 'purchase_date'=>$grn->grnable?->getGrnableDate(),
            'purchase_date' => $request->purchase_date,
            'grn_id' => $grn->id,
            'grn_item_id' => $grnItem->id,
            'category_id' => $grnItem->item->inventory_category_id,
            'item_id' => $grnItem->item_id,
            'item_name' => $grnItem->getItemName(),
            // 'specification'=>$grnItem->purchaseOrderItem->specification,
            'specification' => $request->specification,
            'fiscal_year_id' => $grn->fiscal_year_id,
            'unit_id' => $grnItem->unit_id,
            'account_code_id' => $grnItem->account_code_id,
            'activity_code_id' => $grnItem->activity_code_id,
            'donor_code_id' => $grnItem->donor_code_id,
            'quantity' => $grnItem->quantity,
            'unit_price' => $grnItem->unit_price,
            'total_price' => $grnItem->total_price,
            'vat_amount' => $grnItem->vat_amount,
            'created_by' => auth()->id(),
            'inventory_type' => $grnItem->item->category->getInventoryType(),
            'distribution_type' => $distributionType,
            'asset_flag' => $grnItem->item->category->getInventoryType() != 'Consumable' && $distributionType != 'Distribution',
        ];

        $inventory = $this->grnItems->createInventory($id, $inputs);
        $grnItemsCount = $this->grnItems->where('grn_id', '=', $grn->id)->doesntHave('inventoryItem')->count();
        if ($inventory) {
            if ($ids = array_filter($request->validated()['interpreter_ids'])) {
                foreach (
                    $this->users->whereIn('id', $ids)->get() as $user) {
                    $user->notify(new InventoryPushed($inventory));
                }
            }

            return response()->json(['status' => 'ok',
                'grnItem' => $grnItem,
                'inventory' => $inventory,
                'itemCount' => $grnItemsCount,
                'message' => 'Inventory for GRN item is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Inventory for GRN item can not be added.'], 422);
    }

    public function storeBulk(StoreRequest $request, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $distributionType = $this->distributionTypes->find($request->distribution_type_id)?->title;

        $query = $this->grnItems->where('grn_id', '=', $grn->id)->doesntHave('inventoryItem');
        $grnItems = $query->get();
        $grnItemsIds = $query->pluck('id')->toArray();

        try {
            foreach ($grnItems as $grnItem) {
                $inputs = [
                    'office_id' => $grn->office_id,
                    'distribution_type_id' => $request->distribution_type_id,
                    'execution_id' => $request->execution_id,
                    'supplier_id' => $grn->supplier_id,
                    'purchase_date' => $request->purchase_date,
                    'grn_id' => $grn->id,
                    'grn_item_id' => $grnItem->id,
                    'category_id' => $grnItem->item->inventory_category_id,
                    'item_id' => $grnItem->item_id,
                    'item_name' => $grnItem->getItemName(),
                    'specification' => $request->specification,
                    'fiscal_year_id' => $grn->fiscal_year_id,
                    'unit_id' => $grnItem->unit_id,
                    'account_code_id' => $grnItem->account_code_id,
                    'activity_code_id' => $grnItem->activity_code_id,
                    'donor_code_id' => $grnItem->donor_code_id,
                    'quantity' => $grnItem->quantity,
                    'unit_price' => $grnItem->unit_price,
                    'total_price' => $grnItem->total_price,
                    'vat_amount' => $grnItem->vat_amount,
                    'created_by' => auth()->id(),
                    'inventory_type' => $grnItem->item->category->getInventoryType(),
                    'distribution_type' => $distributionType,
                    'asset_flag' => $grnItem->item->category->getInventoryType() != 'Consumable' && $distributionType != 'Distribution',
                ];

                $inventory = $this->grnItems->createInventory($grnItem->id, $inputs);
            }

            if ($ids = array_filter($request->validated()['interpreter_ids'])) {
                foreach (
                    $this->users->whereIn('id', $ids)->get() as $user) {
                    $user->notify(new InventoryPushedBulk($grn));
                }
            }

            return response()->json(['status' => 'ok',
                'inventory' => $inventory,
                'grnItemsIds' => $grnItemsIds,
                'message' => 'Inventory for GRN items is successfully added in bulk.'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => 'error',
                'message' => 'Inventory for GRN items can not be added in bulk.'], 422);
        }
    }
}
