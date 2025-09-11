<?php

namespace Modules\PurchaseOrder\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderItemRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;

use Modules\PurchaseOrder\Requests\Item\StoreRequest;
use Modules\PurchaseOrder\Requests\Item\UpdateRequest;

use DataTables;
use Modules\PurchaseRequest\Repositories\PurchaseRequestItemRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;

class PurchaseOrderItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param ItemRepository $items
     * @param PurchaseOrderRepository $purchaseOrders
     * @param PurchaseOrderItemRepository $purchaseOrderItems
     * @param UnitRepository $units
     */
    public function __construct(
        AccountCodeRepository       $accountCodes,
        ActivityCodeRepository      $activityCodes,
        DonorCodeRepository         $donorCodes,
        ItemRepository              $items,
        PurchaseOrderRepository     $purchaseOrders,
        PurchaseOrderItemRepository $purchaseOrderItems,
        UnitRepository              $units,
        PurchaseRequestItemRepository $purchaseRequestItems,
        PurchaseRequestRepository $purchaseRequests,
    )
    {
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->items = $items;
        $this->purchaseOrders = $purchaseOrders;
        $this->purchaseOrderItems = $purchaseOrderItems;
        $this->units = $units;
        $this->purchaseRequestItems = $purchaseRequestItems;
        $this->purchaseRequests = $purchaseRequests;
    }

    /**
     * Display a listing of the purchase orders
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $purchaseOrderId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
            $data = $this->purchaseOrderItems->select(['*'])
                ->with(['item', 'unit', 'activityCode', 'accountCode', 'donorCode'])
                ->wherePurchaseOrderId($purchaseOrderId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $purchaseOrder)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $purchaseOrder) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('purchase.orders.items.edit', [$row->purchase_order_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('purchase.orders.items.destroy', [$row->purchase_order_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->addColumn('item', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })->addColumn('activity', function ($row) {
                return $row->activityCode->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->accountCode->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->donorCode->getDonorCode();
            })->addColumn('specification', function ($row) {
                return $row->specification;
            })->addColumn('delivery_date', function ($row) {
                return $row->getDeliveryDate();
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    public function create($id)
    {
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('update', $purchaseOrder);
        $purchaseRequests = $purchaseOrder->purchaseRequests()->select(['id', 'prefix', 'purchase_number', 'modification_number', 'fiscal_year_id'])
                                ->get();
        return view('PurchaseOrder::Item.purchaseItem.create')
                    ->withPurchaseOrder($purchaseOrder)
                    ->withPurchaseRequests($purchaseRequests);

    }

    public function store(Request $request, $poId)
    {
        $inputs = $request->validate([
            'purchase_request_id' => 'required',
        ]);
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        try {
            $purchaseRequest = $this->purchaseRequests->find($inputs['purchase_request_id']);
            if ($purchaseRequest) {
                return redirect()->route('purchase.orders.items.addItem', [$purchaseRequest->id, $purchaseOrder->id]);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->withWarningMessage('Purchase Request not found');
        }
    }

    public function addItem($prId, $poId)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        $purchaseRequest = $this->purchaseRequests->with([
            'purchaseRequestItems',
            'purchaseRequestItems.purchaseOrderItems'
            // ,'purchaseRequestItems.purchaseOrderItem'=>function ($q) {
            //     $q->select(['id','quantity','unit_price','total_price','purchase_request_item_id']);
            // }
            ,'purchaseRequestItems.accountCode'=>function ($q) {
                $q->select(['id','title','description']);
            }, 'purchaseRequestItems.activityCode'=>function ($q) {
                $q->select(['id','title','description']);
            }, 'purchaseRequestItems.donorCode'=>function ($q) {
                $q->select(['id','title','description']);
            }, 'purchaseRequestItems.unit'=>function ($q) {
                $q->select(['id','title']);
            }, 'purchaseRequestItems.item'=>function ($q) {
                $q->select(['id','title', 'item_code']);
            }
            ])->find($prId);
        $lta = $purchaseOrder->ltaContract()->with('ltaItems')->first();
        return view('PurchaseOrder::Item.purchaseItem.edit')
                ->withPurchaseRequest($purchaseRequest)
                ->withLta($lta)
                ->withPurchaseOrder($purchaseOrder);
    }

    public function storeItem(StoreRequest $request,$prId, $poId)
    {
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $prId;
        $purchaseOrder = $this->purchaseOrders->updateFromPr($purchaseOrder->id, $inputs);

        if ($purchaseOrder) {
            $message = 'Purchase order is successfully updated.';
            return redirect()->route('purchase.orders.edit', $poId)
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase order can not be updated.');
    }

    /**
     * Show the form for editing the specified purchase order item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($prId);
        $purchaseOrderItem = $this->purchaseOrderItems->find($id);
        $this->authorize('update', $purchaseOrder);

        $accountCodes = $this->accountCodes->select(['id', 'title', 'description'])
            ->whereNotNull('activated_at')->get();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('PurchaseOrder::Item.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withItems($this->items->get())
            ->withPurchaseOrderItem($purchaseOrderItem)
            ->withUnits($this->units->get());
    }

    /**
     * Update the specified purchase order in storage.
     *
     * @param UpdateRequest $request
     * @param $prId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $purchaseOrderItem = $this->purchaseOrderItems->find($id);
        $this->authorize('update', $purchaseOrderItem->purchaseOrder);
        $inputs = $request->validated();
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $purchaseOrderItem = $this->purchaseOrderItems->update($id, $inputs);
        if ($purchaseOrderItem) {
            return response()->json(['status' => 'ok',
                'purchaseOrderItem' => $purchaseOrderItem,
                'purchaseOrder' => $purchaseOrderItem->purchaseOrder,
                'message' => 'Purchase request item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase request item can not be updated.'], 422);
    }

    /**
     * Remove the specified purchase order from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        $purchaseOrderItem = $this->purchaseOrderItems->find($id);
        $this->authorize('delete', $purchaseOrderItem->purchaseOrder);
        $flag = $this->purchaseOrderItems->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'purchaseOrder' => $purchaseOrderItem->purchaseOrder,
                'message' => 'Purchase order item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase order item can not deleted.',
        ], 422);
    }
}
