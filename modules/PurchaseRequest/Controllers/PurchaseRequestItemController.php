<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\UnitRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestItemRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\Item\StoreRequest;
use Modules\PurchaseRequest\Requests\Item\UpdateRequest;

class PurchaseRequestItemController extends Controller
{
    protected $accountCodes;
    protected $activityCodes;
    protected $districts;
    protected $donorCodes;
    protected $items;
    protected $offices;
    protected $purchaseRequests;
    protected $purchaseRequestItems;
    protected $units;

    /**
     * Create a new controller instance.
     *
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param ItemRepository $items
     * @param PurchaseRequestRepository $purchaseRequests
     * @param PurchaseRequestItemRepository $purchaseRequestItems
     * @param UnitRepository $units
     */
    public function __construct(
        AccountCodeRepository $accountCodes,
        ActivityCodeRepository $activityCodes,
        DistrictRepository $districts,
        DonorCodeRepository $donorCodes,
        ItemRepository $items,
        OfficeRepository $offices,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseRequestItemRepository $purchaseRequestItems,
        UnitRepository $units
    ) {
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
        $this->districts = $districts;
        $this->donorCodes = $donorCodes;
        $this->items = $items;
        $this->offices = $offices;
        $this->purchaseRequests = $purchaseRequests;
        $this->purchaseRequestItems = $purchaseRequestItems;
        $this->units = $units;
    }

    /**
     * Display a listing of the purchase request items
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $purchaseRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
            $data = $this->purchaseRequestItems->select([
                'id', 'purchase_request_id', 'item_id','package_id', 'unit_id', 'activity_code_id', 'account_code_id', 'donor_code_id',
                'quantity', 'unit_price', 'total_price', 'district_id', 'office_id', 'specification',
            ])->with(['item', 'unit', 'activityCode', 'accountCode', 'donorCode'])
                ->wherePurchaseRequestId($purchaseRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $purchaseRequest)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $purchaseRequest) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('purchase.requests.items.edit', [$row->purchase_request_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('purchase.requests.items.destroy', [$row->purchase_request_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->addColumn('item', function ($row) {
                $link = '';
                if($row->package_id){
                    $link = '<a class="text-decoration-none text-dark fw-bold" target="_blank" href="'.route('master.packages.show', $row->package_id). '">' . $row->getPackageName() . '</a>: ';
                }
                $link .= $row->getItemName();
                return $link;
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })
            // ->addColumn('district', function ($row) {
            //     return $row->getDistrict();
            // })
                ->addColumn('office', function ($row) {
                    return $row->getOffice();
                })->addColumn('activity', function ($row) {
                return $row->activityCode->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->accountCode->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->getDonorCode();
            })->rawColumns(['action','item'])
                ->make(true);
        }
        return true;
    }

    public function show($prId)
    {
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $items = $purchaseRequest->purchaseRequestItems()->with('item')->get();
        return view('PurchaseRequest::Item.show')
            ->withItems($items)
            ->withPurchaseRequest($purchaseRequest);
    }

    /**
     * Show the form for creating a new purchase request item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $items = $this->items->getActiveItems();
        $purchaseRequest = $this->purchaseRequests->find($id);
        return view('PurchaseRequest::Item.create')
            ->withActivityCodes($activityCodes)
            ->withDistricts($this->districts->getEnabledDistricts())
            ->withDonorCodes($donorCodes)
            ->withItems($items)
            ->withOffices($this->offices->getActiveOffices())
            ->withPurchaseRequest($purchaseRequest);
    }

    /**
     * Store a newly created purchase request item in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($id);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $purchaseRequest->id;
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $purchaseRequestItem = $this->purchaseRequestItems->create($inputs);

        if ($purchaseRequestItem) {
            return response()->json(['status' => 'ok',
                'purchaseRequestItem' => $purchaseRequestItem,
                'purchaseItemCount' => $purchaseRequestItem->purchaseRequest->purchaseRequestItems()->count(),
                'message' => 'Purchase request item is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase request item can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified purchase request item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $purchaseRequestItem = $this->purchaseRequestItems->find($id);
        $this->authorize('update', $purchaseRequest);

        $accountCodes = $purchaseRequestItem->activityCode ? $purchaseRequestItem->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $items = $this->items->getActiveItems();

        return view('PurchaseRequest::Item.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDistricts($this->districts->getEnabledDistricts())
            ->withOffices($this->offices->getOffices())
            ->withDonorCodes($donorCodes)
            ->withItems($items)
            ->withPurchaseRequestItem($purchaseRequestItem)
            ->withUnits($purchaseRequestItem->item->units->whereNotNull('activated_at'));
    }

    /**
     * Update the specified purchase request item in storage.
     *
     * @param UpdateRequest $request
     * @param $prId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $purchaseRequestItem = $this->purchaseRequestItems->find($id);
        $this->authorize('update', $purchaseRequestItem->purchaseRequest);
        $inputs = $request->validated();
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $purchaseRequestItem = $this->purchaseRequestItems->update($id, $inputs);
        if ($purchaseRequestItem) {
            return response()->json(['status' => 'ok',
                'purchaseRequestItem' => $purchaseRequestItem,
                'purchaseItemCount' => $purchaseRequestItem->purchaseRequest->purchaseRequestItems()->count(),
                'message' => 'Purchase request item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase request item can not be updated.'], 422);
    }

    /**
     * Remove the specified purchase request item from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $purchaseRequestItem = $this->purchaseRequestItems->find($id);
        $this->authorize('update', $purchaseRequestItem->purchaseRequest);
        $flag = $this->purchaseRequestItems->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'purchaseItemCount' => $purchaseRequest->purchaseRequestItems()->count(),
                'message' => 'Purchase request item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase request item can not deleted.',
        ], 422);
    }

    public function destroyAll($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('update', $purchaseRequest);
        $purchaseRequest = $this->purchaseRequestItems->destroyAll($id);
        if ($purchaseRequest) {
            return response()->json([
                'type' => 'success',
                'purchaseItemCount' => $purchaseRequest->purchaseRequestItems()->count(),
                'message' => 'Purchase request items are successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase Request Items can not be deleted.',
        ], 422);
    }

    public function specialIndex(Request $request, $purchaseRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
            $data = $this->purchaseRequestItems->select([
                'id', 'purchase_request_id', 'item_id', 'unit_id', 'activity_code_id', 'account_code_id', 'donor_code_id',
                'quantity', 'unit_price', 'total_price', 'district_id',
            ])->with(['item', 'unit', 'activityCode', 'accountCode', 'donorCode'])
                ->wherePurchaseRequestId($purchaseRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('specialUpdate', $purchaseRequest)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $purchaseRequest) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('purchase.requests.items.special.edit', [$row->purchase_request_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    return $btn;
                });
            }
            return $datatable->addColumn('item', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })
            // ->addColumn('district', function ($row) {
            //     return $row->getDistrict();
            // })
                ->addColumn('office', function ($row) {
                    return $row->getOffice();
                })
                ->addColumn('activity', function ($row) {
                    return $row->activityCode->getActivityCode();
                })->addColumn('account', function ($row) {
                return $row->accountCode->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->getDonorCode();
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    public function specialEdit($prId, $id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $purchaseRequestItem = $this->purchaseRequestItems->find($id);
        $this->authorize('specialUpdate', $purchaseRequest);

        $accountCodes = $purchaseRequestItem->activityCode ? $purchaseRequestItem->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $items = $this->items->getActiveItems();

        return view('PurchaseRequest::Item.Special.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDistricts($this->districts->getEnabledDistricts())
            ->withDonorCodes($donorCodes)
            ->withItems($items)
            ->withOffices($this->offices->getOffices())
            ->withPurchaseRequestItem($purchaseRequestItem)
            ->withUnits($purchaseRequestItem->item->units->whereNotNull('activated_at'));
    }

    public function specialUpdate(Request $request, $prId, $id)
    {
        $purchaseRequestItem = $this->purchaseRequestItems->find($id);
        $this->authorize('specialUpdate', $purchaseRequestItem->purchaseRequest);

        $inputs = $request->validate([
            // 'district_id' => 'required|exists:lkup_districts,id',
            'office_id' => 'required|exists:lkup_offices,id',
        ]);

        $purchaseRequestItem = $this->purchaseRequestItems->update($id, $inputs);
        if ($purchaseRequestItem) {
            return response()->json(['status' => 'ok',
                'purchaseRequestItem' => $purchaseRequestItem,
                'purchaseItemCount' => $purchaseRequestItem->purchaseRequest->purchaseRequestItems()->count(),
                'message' => 'Purchase request item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase request item can not be updated.'], 422);
    }
}
