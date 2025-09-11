<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\CurrencyRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\PurchaseOrder\Combine\StoreRequest;
use Modules\PurchaseRequest\Requests\PurchaseOrder\Combine\UpdateRequest;
use Modules\Supplier\Repositories\SupplierRepository;

class PurchaseOrderCombineController extends Controller
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

    public function create($id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('createOrder', $purchaseRequest);

        $purchaseOrders = $this->purchaseOrders->select('*')
            ->where('status_id', config('constant.CREATED_STATUS'))
            ->whereDoesntHave('purchaseRequests', function ($query) use ($id) {
                $query->where('purchase_request_order.pr_id', $id);
            })
            ->get();
        return view('PurchaseRequest::PurchaseOrder.Combine.create')
            ->withPurchaseRequest($purchaseRequest)
            ->withPurchaseOrders($purchaseOrders);

    }

    public function store(StoreRequest $request, $prId)
    {
        $inputs = $request->validated();
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $this->authorize('createOrder', $purchaseRequest);

        $purchaseOrder = $this->purchaseOrders->find($inputs['purchase_order_id']);
        if ($purchaseOrder) {
            return redirect()->route('purchase.requests.orders.combine.edit', [$prId, $purchaseOrder->id]);
        }
        return redirect()->back()->with('warning', 'Purchase Order not found');
    }

    public function edit($id, $orderId)
    {
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
        $purchaseOrder = $this->purchaseOrders->find($orderId);
        $lta = $purchaseOrder->ltaContract()->with('ltaItems')->first();
        $currencies = $this->currencies->getCurrencies();
        $districts = $this->districts->getEnabledDistricts();
        $suppliers = $this->suppliers->getActiveSuppliers();
        return view('PurchaseRequest::PurchaseOrder.Combine.edit')
            ->withAuthUser(auth()->user())
            ->withCurrencies($currencies)
            ->withDistricts($districts)
            ->withPurchaseRequest($purchaseRequest)
            ->withSuppliers($suppliers)
            ->withLta($lta)
            ->withPurchaseOrder($purchaseOrder);
    }

    public function update(UpdateRequest $request, $prId, $poId)
    {
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $this->authorize('createOrder', $purchaseRequest);
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $purchaseRequest->id;
        $purchaseOrder = $this->purchaseOrders->updateItems($poId, $inputs);
        if ($purchaseOrder) {
            $message = 'Purchase Order updated successfully';
            return redirect()->route('approved.purchase.requests.show', $prId)->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()->withWarningMessage('Purchase order cannot be updated');

    }
}
