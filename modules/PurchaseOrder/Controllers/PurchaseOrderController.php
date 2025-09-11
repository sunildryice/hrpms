<?php

namespace Modules\PurchaseOrder\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PurchaseOrder\Notifications\PurchaseOrderSubmitted;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PurchaseOrder\Requests\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\CurrencyRepository;
use Modules\PurchaseOrder\Notifications\PurchaseOrderCancelSubmitted;
use Modules\Supplier\Repositories\SupplierRepository;

class PurchaseOrderController extends Controller
{
    private $currencies;
    private $districts;
    private $employees;
    private $fiscalYears;
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
     * @param PurchaseOrderRepository $purchaseOrders
     * @param SupplierRepository $suppliers
     * @param UserRepository $users
     */
    public function __construct(
        CurrencyRepository $currencies,
        DistrictRepository $districts,
        EmployeeRepository     $employees,
        FiscalYearRepository   $fiscalYears,
        PurchaseOrderRepository $purchaseOrders,
        SupplierRepository $suppliers,
        UserRepository         $users
    )
    {
        $this->currencies = $currencies;
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseOrders = $purchaseOrders;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->destinationPath = 'purchaseOrder';
    }
    /**
     * Display a listing of the purchase orders
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $this->authorize('purchase-order');
        if ($request->ajax()) {
            $data = $this->purchaseOrders->with(['fiscalYear', 'status', 'createdBy', 'logs'])->select(['*'])
                ->whereCreatedBy($authUser->id)
                ->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })
                ->orderBy('fiscal_year_id','desc')
                ->orderBy('order_number', 'desc')->get();

            $data = $data->sortBy(function ($item, $index) {
                if ($item->status_id == config('constant.CREATED_STATUS')) {
                    return -1;
                }
                return $index;
            });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('order_date', function ($row) {
                    return $row->getOrderDate();
                })->addColumn('delivery_date', function ($row) {
                    return $row->getDeliveryDate();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('order_number', function ($row) {
                    return $row->getPurchaseOrderNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('purchase.orders.show', $row->id) . '" rel="tooltip" title="View Purchase Order"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('purchase.orders.edit', $row->id) . '" rel="tooltip" title="Edit Purchase Order"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('purchase.orders.destroy', $row->id) . '" rel="tooltip" title="Delete Purchase Order">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if($authUser->can('cancel', $row)){
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-outline-danger cancel-record"';
                        $btn .= 'data-href = "' . route('purchase.orders.cancel', $row->id) . '" data-number="' . $row->getPurchaseOrderNumber() . '" title="Cancel PO">';
                        $btn .= '<i class="bi bi-x-lg" ></i></a>';
                    }
                    // if($authUser->can('reverse', $row)){
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-sm btn-outline-danger amend-record"';
                    //     $btn .= 'data-href = "' . route('purchase.orders.reverse', $row->id) . '" data-number="' . $row->getPurchaseOrderNumber() . '" title="Reverse Purchase Order">';
                    //     $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    // }
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseOrder::index');
    }
    /**
     * Show the specified purchase order.
     *
     * @param $purchaseOrderId
     * @return mixed
     */
    public function show($purchaseOrderId)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
        return view('PurchaseOrder::show')
            ->withPurchaseOrder($purchaseOrder);
    }
    /**
     * Show the form for editing the specified purchase order.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('update', $purchaseOrder);

        $approvers  = $this->users->permissionBasedUsers('approve-purchase-order');
        $currencies = $this->currencies->getCurrencies();
        $districts  = $this->districts->getEnabledDistricts();
        $reviewers  = $this->users->permissionBasedUsers('review-purchase-order');
        $suppliers  = $this->suppliers->getActiveSuppliers();
        return view('PurchaseOrder::edit')
            ->withAuthUser(auth()->user())
            ->withApprovers($approvers)
            ->withCurrencies($currencies)
            ->withDistricts($districts)
            ->withPurchaseOrder($purchaseOrder)
            ->withReviewers($reviewers)
            ->withSuppliers($suppliers);
    }
    /**
     * Update the specified purchase order in storage.
     *
     * @param \Modules\PurchaseOrder\Requests\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('update', $purchaseOrder);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseOrder = $this->purchaseOrders->update($id, $inputs);

        if ($purchaseOrder) {
            $message = 'Purchase order is successfully updated.';
            if ($purchaseOrder->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Purchase order is successfully submitted.';
                $purchaseOrder->reviewer->notify(new PurchaseOrderSubmitted($purchaseOrder));
            }else if( $purchaseOrder->status_id == config('constant.CREATED_STATUS') ){
                return redirect()->back()->withInput()
                        ->withSuccessMessage('Purchase order updated.');
            }
            return redirect()->route('purchase.orders.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('Purchase order can not be updated.');
    }

    /**
     * Remove the specified purchase order from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('delete', $purchaseOrder);
        $flag = $this->purchaseOrders->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Purchase order is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Purchase order can not deleted.',
        ], 422);
    }

    /**
     * Show the specified purchase order in printable view
     *
     * @param $id
     * @return mixed
     */
    public function printOrder($id)
    {
        $authUser = auth()->user();

        $purchaseOrder = $this->purchaseOrders->find($id);
        $items = $purchaseOrder->purchaseOrderItems;
        $groupedItems = $items->groupBy(['item_id', 'account_code_id', 'activity_code_id', 'donor_code_id','unit_price'])->flatten(4)
            ->map(function ($group) {
                $poItem = collect();
                foreach ($group as $index => $item) {
                    if ($index == 0) {
                        $poItem = $item;
                        // $poItem->unit_price = $poItem->quantity . ' x ' . $poItem->unit_price;
                        continue;
                    }
                    $poItem->quantity += $item->quantity;
                    $poItem->total_price += $item->total_price;
                    // $poItem->unit_price .= ' + ' . $item->quantity . ' x ' . $item->unit_price;
                }
                return $poItem;
            });
        // dd($groupedItems->toArray());
        return view('PurchaseOrder::print')
            ->withItems($groupedItems)
            ->withPurchaseOrder($purchaseOrder);
    }

    public function reverse(Request $request, $poId)
    {
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);
        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('reverse', $purchaseOrder);
        $purchaseOrder = $this->purchaseOrders->reverseApprove($poId, $inputs);
        if($purchaseOrder){
            return response()->json(['status' => 'success', 'message' => 'Purchase Order reversed successfully'], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to reverse Purchase Order'], 422);
    }

    public function cancel(Request $request, $poId)
    {
        $inputs = $request->validate([
            'log_remarks' => 'required|string',
        ]);

        $purchaseOrder = $this->purchaseOrders->find($poId);
        $this->authorize('cancel', $purchaseOrder);
        $purchaseOrder = $this->purchaseOrders->requestCancel($poId, $inputs);

        if($purchaseOrder){
            if($purchaseOrder->status_id == config('constant.INIT_CANCEL_STATUS')){
                $purchaseOrder->approver->notify(new PurchaseOrderCancelSubmitted($purchaseOrder));
                return response()->json(['status' => 'success', 'message' => 'Purchase Order cancel requested successfully'], 200);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Failed to request Purchase Order cancellation'], 422);
    }
}
