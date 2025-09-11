<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\GoodRequest\Repositories\GoodRequestItemRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;

use Modules\GoodRequest\Requests\Item\StoreRequest;
use Modules\GoodRequest\Requests\Item\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\UnitRepository;

class GoodRequestItemController extends Controller
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
     * Display a listing of the good request items
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $goodRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $goodRequest = $this->goodRequests->find($goodRequestId);
            $data = $this->goodRequestItems->select(['*'])
                ->with(['activityCode', 'accountCode', 'donorCode', 'unit'])
                ->whereGoodRequestId($goodRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('assigned_quantity', function ($row) {
                    return $row->assigned_quantity;
                });
            $datatable->addColumn('action', function ($row) use ($authUser, $goodRequest) {
                $btn = '';
                if($authUser->can('update', $goodRequest)) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('good.requests.items.edit', [$row->good_request_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('good.requests.items.destroy', [$row->good_request_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                } else if($authUser->can('approve', $goodRequest)){
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" title="Assign Item" href="';
                    $btn .= route('assign.good.requests.items.create', [$row->good_request_id, $row->id]) . '"><i class="bi-bag-check-fill"></i></a>';
                }
                return $btn;
            });

            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->getDonorCode();
            })->addColumn('unit', function ($row) {
                return $row->getUnit();
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new good request item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $goodRequest = $this->goodRequests->find($id);
        $units = $this->units->getActiveUnits();
        return view('GoodRequest::Item.create')
            ->withGoodRequest($goodRequest)
            ->withUnits($units);
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
        $goodRequest = $this->goodRequests->find($id);
        $inputs = $request->validated();
        $inputs['good_request_id'] = $goodRequest->id;
        $goodRequestItem = $this->goodRequestItems->create($inputs);

        if ($goodRequestItem) {
            return response()->json(['status' => 'ok',
                'goodRequest' => $goodRequestItem->goodRequest,
                'goodRequestItem' => $goodRequestItem,
                'message' => 'Good request item is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Good request item can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified good request item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($drId, $id)
    {
        $goodRequest = $this->goodRequests->find($drId);
        $goodRequestItem = $this->goodRequestItems->find($id);
        $this->authorize('update', $goodRequest);

        $units = $this->units->getActiveUnits();
        return view('GoodRequest::Item.edit')
            ->withGoodRequestItem($goodRequestItem)
            ->withUnits($units);
    }

    /**
     * Update the specified good request item in storage.
     *
     * @param UpdateRequest $request
     * @param $drId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $drId, $id)
    {
        $goodRequestItem = $this->goodRequestItems->find($id);
        $this->authorize('update', $goodRequestItem->goodRequest);
        $inputs = $request->validated();
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

    /**
     * Remove the specified good request item from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($drId, $id)
    {
        $goodRequestItem = $this->goodRequestItems->find($id);
        $this->authorize('delete', $goodRequestItem->goodRequest);
        $flag = $this->goodRequestItems->destroy($id);
        if ($flag) {
            $goodRequest = $this->goodRequests->find($drId);
            return response()->json([
                'type' => 'success',
                'goodRequest' => $goodRequest,
                'message' => 'Good request item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'goodRequest' => $goodRequestItem->goodRequest,
            'message' => 'Good request item can not deleted.',
        ], 422);
    }
}
