<?php

namespace Modules\PurchaseOrder\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseOrder\Notifications\PurchaseOrderApproved;
use Modules\PurchaseOrder\Notifications\PurchaseOrderRejected;
use Modules\PurchaseOrder\Notifications\PurchaseOrderReturned;
use Modules\PurchaseOrder\Notifications\PurchaseOrderSubmitted;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PurchaseOrder\Requests\Approve\StoreRequest;
use DataTables;
use Modules\PurchaseOrder\Notifications\PurchaseOrderCancelApproved;
use Modules\PurchaseOrder\Notifications\PurchaseOrderCancelRejected;

class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseOrderRepository $purchaseOrders
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        PurchaseOrderRepository $purchaseOrders,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseOrders = $purchaseOrders;
        $this->users = $users;
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
        if ($request->ajax()) {
            $data = $this->purchaseOrders->with(['fiscalYear', 'status', 'createdBy'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.VERIFIED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.RECOMMENDED_STATUS'));
                })->orderBy('order_number', 'desc')->get();

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
                    $btn .= route('approve.purchase.orders.create', $row->id) . '" rel="tooltip" title="Approve Purchase Order">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseOrder::Approve.index');
    }

    public function create($purchaseOrderId)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
        $this->authorize('approve', $purchaseOrder);

        return view('PurchaseOrder::Approve.create')
            ->withAuthUser($authUser)
            ->withPurchaseOrder($purchaseOrder);
    }

    public function store(StoreRequest $request, $purchaseOrderId)
    {
        $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
        $this->authorize('approve', $purchaseOrder);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseOrder = $this->purchaseOrders->approve($purchaseOrder->id, $inputs);

        if ($purchaseOrder) {
            $message = '';
            if ($purchaseOrder->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Purchase order is successfully returned.';
                $purchaseOrder->createdBy->notify(new PurchaseOrderReturned($purchaseOrder));
            } else {
                $message = 'Purchase order is successfully approved.';
                $purchaseOrder->createdBy->notify(new PurchaseOrderApproved($purchaseOrder));
            }

            return redirect()->route('approve.purchase.orders.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Purchase order can not be approved.');
    }

    public function cancelIndex(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->purchaseOrders->with(['fiscalYear', 'status', 'createdBy'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('status_id', config('constant.INIT_CANCEL_STATUS'));
                })
                ->orderBy('order_number', 'desc')->get();

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
                    $btn .= route('approve.purchase.orders.cancel.create', $row->id) . '" rel="tooltip" title="Cancel Purchase Order">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseOrder::Cancel.index');
    }

    public function cancelCreate($purchaseOrderId)
    {
        $authUser = auth()->user();
        $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
        $this->authorize('approveCancel', $purchaseOrder);

        return view('PurchaseOrder::Cancel.create')
            ->withAuthUser($authUser)
            ->withPurchaseOrder($purchaseOrder);
    }

    public function cancelStore(StoreRequest $request, $purchaseOrderId)
    {
        $purchaseOrder = $this->purchaseOrders->find($purchaseOrderId);
        $this->authorize('approveCancel', $purchaseOrder);
        $inputs = $request->validated();

        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseOrder = $this->purchaseOrders->cancel($purchaseOrder->id, $inputs);

        if ($purchaseOrder) {
            $message = '';
            if ($purchaseOrder->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Cancellation Purchase order rejected.';
                $purchaseOrder->createdBy->notify(new PurchaseOrderCancelRejected($purchaseOrder));
            } else {
                $message = 'Cancellation of Purchase order is approved.';
                $purchaseOrder->createdBy->notify(new PurchaseOrderCancelApproved($purchaseOrder));
            }

            return redirect()->route('approve.purchase.orders.cancel.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Purchase order can not be updated.');
    }

}
