<?php

namespace Modules\DistributionRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\DistributionRequest\Repositories\DistributionRequestItemRepository;
use Modules\DistributionRequest\Repositories\DistributionRequestRepository;
use Modules\DistributionRequest\Requests\Item\StoreRequest;
use Modules\DistributionRequest\Requests\Item\UpdateRequest;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;

class DistributionRequestItemController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected DistributionRequestRepository $distributionRequests,
        protected DistributionRequestItemRepository $distributionRequestItems,
        protected DonorCodeRepository $donorCodes,
        protected InventoryItemRepository $inventoryItems,
        protected ItemRepository $items,
        protected UnitRepository $units
    ) {}

    /**
     * Display a listing of the distribution request items
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $distributionRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $distributionRequest = $this->distributionRequests->find($distributionRequestId);
            $data = $this->distributionRequestItems->select(['*'])
                ->with(['activityCode', 'accountCode', 'donorCode', 'item', 'unit'])
                ->whereDistributionRequestId($distributionRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            $datatable->addColumn('action', function ($row) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                $btn .= route('distribution.requests.items.edit', [$row->distribution_request_id, $row->id]).'" rel="tooltip" title="Edit Distribution Request Item""><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="'.route('distribution.requests.items.destroy', [$row->distribution_request_id, $row->id]).'" rel="tooltip" title="Delete Distribution Request Item"">';
                $btn .= '<i class="bi-trash"></i></a>';

                return $btn;
            });

            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->getDonorCode();
            })->addColumn('item_name', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnit();
            })->rawColumns(['action'])
                ->make(true);
        }

        return true;
    }

    /**
     * Show the form for creating a new distribution request item.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $distributionRequest = $this->distributionRequests->find($id);
        $inventoryItems = $this->inventoryItems->select(['id', 'assigned_quantity', 'quantity'])
            ->where('distribution_type_id', 2)
            ->get();
        $inventoryItemIds = $inventoryItems->filter(function ($inventoryItem) {
            return $inventoryItem->quantity > $inventoryItem->assigned_quantity;
        })->pluck('id')->toArray();

        $inventoryItems = $this->inventoryItems->with(['item', 'grn.fiscalYear'])
            ->whereIn('id', $inventoryItemIds)->get();

        return view('DistributionRequest::Item.create')
            ->withActivityCodes($activityCodes)
            ->withDistributionRequest($distributionRequest)
            ->withDonorCodes($donorCodes)
            ->withInventoryItems($inventoryItems);
    }

    /**
     * Store a newly created distribution request item in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $distributionRequest = $this->distributionRequests->find($id);
        $inputs = $request->validated();
        $inventoryItem = $this->inventoryItems->find($inputs['inventory_item_id']);
        $inputs['distribution_request_id'] = $distributionRequest->id;
        $inputs['item_id'] = $inventoryItem->item_id;

        $inputs['unit_id'] = $inventoryItem->unit_id;
        $inputs['total_amount'] = $inputs['quantity'] * $inputs['unit_price'];
        $inputs['vat_amount'] = 0;
        if ($inventoryItem->vat_amount > 0) {
            $inputs['vat_amount'] = $inputs['total_amount'] * config('constant.VAT_PERCENTAGE') / 100;
        }

        $distributionRequestItem = $this->distributionRequestItems->create($inputs);

        if ($distributionRequestItem) {
            $distributionRequest = $distributionRequestItem->distributionRequest;
            $distributionItems = $distributionRequest->distributionRequestItems()->select(['vat_amount', 'total_amount'])->get();

            return response()->json(['status' => 'ok',
                'distributionRequest' => $distributionRequest,
                'distributionRequestItem' => $distributionRequestItem,
                'total_amount' => $distributionItems->sum('total_amount'),
                'total_vat' => $distributionItems->sum('vat_amount'),
                'message' => 'Distribution request item is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Distribution request item can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified distribution request item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($drId, $id)
    {
        $distributionRequest = $this->distributionRequests->find($drId);
        $distributionRequestItem = $this->distributionRequestItems->find($id);
        $this->authorize('update', $distributionRequest);

        $accountCodes = $distributionRequestItem->activityCode ? $distributionRequestItem->activityCode->accountCodes
            ->whereNotNull('activated_at') : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $inventoryItems = $this->inventoryItems->select(['id', 'assigned_quantity', 'quantity'])
            ->where('distribution_type_id', 2)
            ->get();
        $inventoryItemIds = $inventoryItems->filter(function ($inventoryItem, $distributionRequestItem) {
            return $inventoryItem->quantity > $inventoryItem->assigned_quantity;
        })->pluck('id')->toArray();
        $inventoryItemIds[] = $distributionRequestItem->inventory_item_id;

        $inventoryItems = $this->inventoryItems->with(['item', 'grn.fiscalYear'])
            ->whereIn('id', $inventoryItemIds)->get();

        return view('DistributionRequest::Item.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withInventoryItems($inventoryItems)
            ->withDistributionRequestItem($distributionRequestItem);
    }

    /**
     * Update the specified distribution request item in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $drId, $id)
    {
        $distributionRequestItem = $this->distributionRequestItems->find($id);
        $this->authorize('update', $distributionRequestItem->distributionRequest);
        $inputs = $request->validated();
        //        $inventoryItem = $this->inventoryItems->find($inputs['inventory_item_id']);
        //        $inputs['item_id'] = $inventoryItem->item_id;
        //        $inputs['unit_id'] = $inventoryItem->unit_id;
        $inputs['total_amount'] = $inputs['quantity'] * $inputs['unit_price'];
        $inputs['vat_amount'] = 0;
        if ($distributionRequestItem->inventoryItem?->vat_amount > 0) {
            $inputs['vat_amount'] = $inputs['total_amount'] * config('constant.VAT_PERCENTAGE') / 100;
        }

        $distributionRequestItem = $this->distributionRequestItems->update($id, $inputs);
        $distributionRequest = $distributionRequestItem->distributionRequest;
        $distributionItems = $distributionRequest->distributionRequestItems()->select(['vat_amount', 'total_amount'])->get();
        if ($distributionRequestItem) {
            return response()->json(['status' => 'ok',
                'distributionRequest' => $distributionRequest,
                'distributionRequestItem' => $distributionRequestItem,
                'total_amount' => $distributionItems->sum('total_amount'),
                'total_vat' => $distributionItems->sum('vat_amount'),
                'message' => 'Distribution request item is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Distribution request item can not be updated.'], 422);
    }

    /**
     * Remove the specified distribution request item from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($drId, $id)
    {
        $distributionRequestItem = $this->distributionRequestItems->find($id);
        $this->authorize('delete', $distributionRequestItem->distributionRequest);
        $flag = $this->distributionRequestItems->destroy($id);
        if ($flag) {
            $distributionRequest = $this->distributionRequests->find($drId);
            $distributionItems = $distributionRequest->distributionRequestItems()->select(['vat_amount', 'total_amount'])->get();

            return response()->json([
                'type' => 'success',
                'distributionRequest' => $distributionRequest,
                'total_amount' => $distributionItems->sum('total_amount'),
                'total_vat' => $distributionItems->sum('vat_amount'),
                'message' => 'Distribution request item is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'distributionRequest' => $distributionRequestItem->distributionRequest,
            'message' => 'Distribution request item can not deleted.',
        ], 422);
    }
}
