<?php

namespace Modules\GoodRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\GoodRequestAssigned;
use Modules\GoodRequest\Notifications\GoodRequestRejected;
use Modules\GoodRequest\Notifications\GoodRequestReturned;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\GoodRequest\Requests\Assign\StoreRequest;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Privilege\Repositories\UserRepository;

class AssignController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected GoodRequestRepository $goodRequests,
        protected InventoryItemRepository $inventoryItems,
        protected ItemRepository $items,
        protected UserRepository $users
    ) {}

    /**
     * Display a listing of the good requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->with(['status', 'requester'])
                ->where(function ($q) use ($authUser) {
                    $q->where('logistic_officer_id', $authUser->id);
                    $q->where('status_id', config('constant.APPROVED_STATUS'));
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('assign.good.requests.create', $row->id).'" rel="tooltip" title="Assign good Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::Assign.index');
    }

    public function create($goodRequestId)
    {
        $authUser = auth()->user();
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('assign', $goodRequest);

        $inventoryItems = $this->inventoryItems->with([
            'item',
            'assets',
            'assets.latestGoodRequestAsset',
            'category',
            'category.inventoryType',

        ])
            ->where('distribution_type_id', 1)
            ->whereColumn('quantity', '>', 'assigned_quantity')
            ->orderBy('purchase_date', 'desc')
            ->get();

        $inventoryItems = $inventoryItems->map(function ($inventoryItem) {
            if ($inventoryItem->assets->count() == 1 && $inventoryItem->batch_number == null) {
                $inventoryItem->item_name .= ' '.$inventoryItem->assets->first()->getAssetNumber();
            }

            return $inventoryItem;
        });

        return view('GoodRequest::Assign.create')
            ->withAuthUser($authUser)
            ->withGoodRequest($goodRequest)
            ->withInventoryItems($inventoryItems);
    }

    /**
     *  Store a newly assigned asset items to good request in storage.
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request, $goodRequestId)
    {
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('assign', $goodRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        if ($inputs['status_id'] == config('constant.ASSIGNED_STATUS')) {
            $requestedQuantities = [];
            foreach ($inputs['assigned_quantity'] as $key => $quantity) {
                if ($quantity === null) {
                    continue;
                }

                $inventoryItemId = $inputs['assigned_inventory_item_id'][$key];
                $requestedQuantities[$inventoryItemId] = ($requestedQuantities[$inventoryItemId] ?? 0) + $quantity;
            }
            foreach ($requestedQuantities as $inventoryItemId => $totalRequested) {
                $inventoryItem = $this->inventoryItems->find($inventoryItemId);
                $available = $inventoryItem->getAvailableQuantity();

                if ($available < $totalRequested) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrorMessage('Total assigned quantity ('.$totalRequested.') exceeds available quantity ('.$available.') for item: '.$inventoryItem->item_name);
                }
            }
        }

        $goodRequest = $this->goodRequests->assign($goodRequest->id, $inputs);

        if ($goodRequest) {
            $message = '';
            if ($goodRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Good request is successfully returned.';
                $goodRequest->requester->notify(new GoodRequestReturned($goodRequest));
            } elseif ($goodRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Good request is successfully rejected.';
                $goodRequest->requester->notify(new GoodRequestRejected($goodRequest));
            } elseif ($goodRequest->status_id == config('constant.ASSIGNED_STATUS')) {
                $message = 'Good request is successfully assigned.';
                $goodRequest->requester->notify(new GoodRequestAssigned($goodRequest));
            }

            return redirect()->route('assign.good.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Good request can not be assigned.');
    }
}
