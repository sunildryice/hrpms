<?php

namespace Modules\PurchaseOrder\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class CancelledController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $purchaseOrders;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseOrderRepository $purchaseOrders
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository      $employees,
        FiscalYearRepository    $fiscalYears,
        PurchaseOrderRepository $purchaseOrders,
        UserRepository          $users
    )
    {
        $this->employees        = $employees;
        $this->fiscalYears      = $fiscalYears;
        $this->purchaseOrders   = $purchaseOrders;
        $this->users            = $users;
    }

    /**
     * Display a listing of the purchase requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->purchaseOrders->getCancelled();

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
                    $btn .= route('cancelled.purchase.orders.show', $row->id) . '" rel="tooltip" title="View Purchase Order">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                    $btn .= route('purchase.orders.print', $row->id) . '" rel="tooltip" title="Print Purchase Order"><i class="bi bi-printer"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseOrder::Cancelled.index');
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
        $this->authorize('viewApproved', $purchaseOrder);
        $poItemArray = $purchaseOrder->purchaseOrderItems()->pluck('id');

        return view('PurchaseOrder::Cancelled.show')
            ->withPurchaseOrder($purchaseOrder);
    }
}
