<?php

namespace Modules\PurchaseOrder\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Requests\Grn\StoreRequest;
use Modules\Supplier\Repositories\SupplierRepository;

class GrnController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GrnRepository $grns
     * @param PurchaseOrderRepository $purchaseOrders
     * @param SupplierRepository $suppliers
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository        $districts,
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        GrnRepository             $grns,
        PurchaseOrderRepository   $purchaseOrders,
        SupplierRepository        $suppliers,
        UserRepository            $users
    )
    {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->grns = $grns;
        $this->purchaseOrders = $purchaseOrders;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->destinationPath = 'purchaseOrder';
    }

    /**
     * Display a listing of the grns
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $purchaseOrder = $this->purchaseOrders->find($id);
            // $data = $this->grns->with(['supplier'=>function($q){
            //     $q->select(['id', 'supplier_name']);
            // }, 'status', 'PurchaseOrder'=>function($q){
            //     $q->select(['id','prefix','order_number']);
            // }])->select(['*'])
            //     ->wherePurchaseOrderId($purchaseOrder->id);

            $data = $purchaseOrder->grns;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('received_date', function ($row) {
                    return $row->getReceivedDate();
                })->addColumn('order_number', function ($row) {
                    return $row->grnable->getPurchaseOrderNumber();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('grn_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('grns.show', $row->id) . '" rel="tooltip" title="View GRN"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('grns.edit', $row->id) . '" rel="tooltip" title="Edit GRN"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('grns.destroy', $row->id).'"'. 'rel="tooltip" title="Delete GRN">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'grn_number', 'supplier', 'status'])
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
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('createGrn', $purchaseOrder);

        $districts = $this->districts->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name'])->whereNotNull('activated_at')->get();
        return view('PurchaseOrder::Grn.create')
            ->withAuthUser(auth()->user())
            ->withDistricts($districts)
            ->withPurchaseOrder($purchaseOrder)
            ->withSuppliers($suppliers);
    }

    /**
     * Store the specified purchase order of a purchase request in storage.
     *
     * @param \Modules\PurchaseOrder\Requests\Grn\StoreRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $purchaseOrder = $this->purchaseOrders->find($id);
        $this->authorize('createGrn', $purchaseOrder);
        $inputs = $request->validated();
        $inputs['purchase_order_id'] = $purchaseOrder->id;
        $inputs['supplier_id'] = $purchaseOrder->supplier_id;
        $inputs['office_id'] = $purchaseOrder->office_id;
        $inputs['created_by'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $grn = $this->grns->createFromPo($inputs);
        if ($grn) {
            $message = 'GRN is successfully created.';
            return redirect()->route('approved.purchase.orders.show', $id)
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('GRN can not be added. Received quantity can not be zero and can not exceed total order quantity for a item.');
    }
}
