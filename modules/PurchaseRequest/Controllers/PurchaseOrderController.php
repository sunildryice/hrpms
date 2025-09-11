<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\CurrencyRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PurchaseOrder\Notifications\PurchaseOrderSubmitted;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\PurchaseOrder\StoreRequest;
use Modules\PurchaseRequest\Requests\PurchaseOrder\UpdateRequest;
use Modules\Supplier\Repositories\SupplierRepository;

class PurchaseOrderController extends Controller
{
    private $currencies;
    private $districts;
    private $employees;
    private $fiscalYears;
    private $purchaseRequests;
    private $purchaseOrders;
    private $suppliers;
    private $users;
    private $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseRequestRepository $purchaseRequests
     * @param PurchaseOrderRepository $purchaseOrders
     * @param SupplierRepository $suppliers
     * @param UserRepository $users
     */
    public function __construct(
        CurrencyRepository $currencies,
        DistrictRepository $districts,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseOrderRepository $purchaseOrders,
        SupplierRepository $suppliers,
        UserRepository $users
    ) {
        $this->currencies = $currencies;
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseRequests = $purchaseRequests;
        $this->purchaseOrders = $purchaseOrders;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->destinationPath = 'purchaseOrder';
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $purchaseRequest = $this->purchaseRequests->find($id);
            $prItemArray = $purchaseRequest->purchaseRequestItems()->pluck('id');
            $data = $purchaseRequest->purchaseOrders()->with(['supplier', 'status']);
                
            // $data = $this->purchaseOrders->with(['supplier', 'status'])->select(['*'])
            //     ->whereHas('purchaseOrderItems', function ($q) use ($prItemArray) {
            //         $q->whereIn('purchase_request_item_id', $prItemArray);
            //     })->orWhereHas('purchaseRequests', function ($q) use ($id) {
            //         $q->where('pr_id', $id);
            //     });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('order_date', function ($row) {
                    return $row->getOrderDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('order_number', function ($row) {
                    return $row->getPurchaseOrderNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser, $id) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('purchase.orders.show', $row->id) . '" rel="tooltip" title="View Purchase Order"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.purchase.requests.orders.edit', [$id,$row->id]) . '" rel="tooltip" title="Edit Purchase Order"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('purchase.orders.destroy', $row->id) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'order_number', 'supplier', 'status'])
                ->make(true);
        }
    }

    /**
     * Show the form for editing the specified purchase request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
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
            ])->find($id);
        $this->authorize('createOrder', $purchaseRequest);
        $items = $purchaseRequest->purchaseRequestItems;

        $districts = $this->districts->getEnabledDistricts();
        $suppliers = $this->suppliers->getActiveSuppliers();
        $currencies = $this->currencies->getCurrencies();

        return view('PurchaseRequest::PurchaseOrder.create')
            ->withAuthUser(auth()->user())
            ->withCurrencies($currencies)
            ->withDistricts($districts)
            ->withPurchaseRequest($purchaseRequest)
            ->withItems($items)
            ->withSuppliers($suppliers);
    }

    /**
     * Store the specified purchase order of a purchase request in storage.
     *
     * @param \Modules\PurchaseRequest\Requests\PurchaseOrder\StoreRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('createOrder', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $purchaseRequest->id;
        $inputs['office_id'] = $purchaseRequest->office_id;
        $inputs['created_by'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseOrder = $this->purchaseOrders->create($inputs);

        if ($purchaseOrder) {
            $message = 'Purchase order is successfully created.';
            if($request->ajax()){
                return response()->json(['status'=>'ok','message' => $message, 'id' => $purchaseOrder->id], 200);
            }
            return redirect()->route('approved.purchase.requests.show', $id)
                ->withSuccessMessage($message);
        }
        if($request->ajax()){
            return response()->json(['status'=>'error','message' => 'Purchase order can not be added.'], 422);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase order can not be added.');
    }

    /** */
    public function edit($prId, $poId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        $approvers  = $this->users->permissionBasedUsers('approve-purchase-order');
        $reviewers  = $this->users->permissionBasedUsers('review-purchase-order');

        $districts = $this->districts->getEnabledDistricts();
        $suppliers = $this->suppliers->getActiveSuppliers();
        $currencies = $this->currencies->getCurrencies();
        return view('PurchaseRequest::PurchaseOrder.edit')
            ->withAuthUser(auth()->user())
            ->withCurrencies($currencies)
            ->withDistricts($districts)
            ->withPurchaseRequest($purchaseRequest)
            ->withPurchaseOrder($purchaseOrder)
            ->withReviewers($reviewers)
            ->withApprovers($approvers)
            ->withSuppliers($suppliers);
    }

    public function createItem($id)
    {
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('update', $purchaseOrder);
        $prIds = $purchaseOrder->purchaseRequests()->select(['id', 'prefix', 'purchase_number', 'modification_number', 'fiscal_year_id'])
                                ->get();
        return view('PurchaseRequest::PurchaseOrder.Item.create')
                    ->withPurchaseOrder($purchaseOrder)
                    ->withPrIds($prIds);
    }

    public function editItem(Request $request, $poId)
    {
        $inputs = $request->validate([
            'purchase_request_id' => 'required',
        ]);
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        try {
            $purchaseRequest = $this->purchaseRequests->find($inputs['purchase_request_id']);
            if ($purchaseRequest) {
                return redirect()->route('approved.purchase.requests.orders.addItem', [$purchaseRequest->id, $purchaseOrder->id]);
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
        return view('PurchaseRequest::PurchaseOrder.Item.edit')
                ->withPurchaseRequest($purchaseRequest)
                ->withLta($lta)
                ->withPurchaseOrder($purchaseOrder);
    }

    public function updateItem(UpdateRequest $request, $prId, $poId)
    {
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('update', $purchaseOrder);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $prId;
        $purchaseOrder = $this->purchaseOrders->updateFromPr($purchaseOrder->id, $inputs);

        if ($purchaseOrder) {
            $message = 'Purchase order is successfully updated.';
            return redirect()->route('approved.purchase.requests.orders.edit', [$prId, $poId])
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase order can not be updated.');
    }
}
