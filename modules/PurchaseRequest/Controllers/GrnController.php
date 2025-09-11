<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Grn\Repositories\GrnRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseRequest\Requests\Grn\StoreRequest;
use Modules\PurchaseRequest\Requests\Grn\UpdateRequest;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
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
        DistrictRepository $districts,
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        GrnRepository $grns,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseOrderRepository $purchaseOrders,
        SupplierRepository $suppliers,
        UserRepository $users
    ) {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->grns = $grns;
        $this->purchaseRequests = $purchaseRequests;
        $this->suppliers = $suppliers;
        $this->users = $users;
        $this->purchaseOrders = $purchaseOrders;
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
            $purchaseRequest = $this->purchaseRequests->find($id);
            $data = $purchaseRequest->grns;

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('received_date', function ($row) {
                    return $row->getReceivedDate();
                })->addColumn('request_number', function ($row) {
                    return $row->grnable->getPurchaseRequestNumber();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('grn_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser, $id) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('grns.show', $row->id) . '" rel="tooltip" title="View GRN"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('approved.purchase.requests.grns.edit', [$id, $row->id]) . '" rel="tooltip" title="Edit GRN"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('grns.destroy', $row->id) . '" rel="tooltip" title="Delete GRN">';
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
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('createGrn', $purchaseRequest);

        $districts = $this->districts->get();
        $suppliers = $this->suppliers->select(['id', 'supplier_name'])->whereNotNull('activated_at')->get();
        return view('PurchaseRequest::Grn.create')
            ->withAuthUser(auth()->user())
            ->withDistricts($districts)
            ->withPurchaseRequest($purchaseRequest)
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
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('createGrn', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $purchaseRequest->id;
        // $inputs['supplier_id'] = $purchaseRequest->supplier_id;
        $inputs['office_id'] = $purchaseRequest->office_id;
        $inputs['created_by'] = $inputs['updated_by'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $grn = $this->grns->createFromPr($inputs);
        if ($grn) {
            $message = 'GRN is successfully created.';
            return redirect()->route('approved.purchase.requests.show', $id)
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('GRN can not be added. Received quantity can not be zero and can not exceed total order quantity for a item.');
    }

    public function edit($prId, $grnId)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $purchaseRequest = $this->purchaseRequests->find($prId);
        $suppliers = $this->suppliers->getActiveSuppliers();
        return view('PurchaseRequest::Grn.edit')
            ->withAuthUser($authUser)
            ->withGrn($grn)
            ->withPurchaseRequest($purchaseRequest)
            ->withSuppliers($suppliers);
    }

    public function addItem($prId, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $purchaseRequest = $this->purchaseRequests->find($prId);
        return view('PurchaseRequest::Grn.Item.create')
                    ->withPurchaseRequest($purchaseRequest)
                    ->withGrn($grn);
    }

    public function updateItem(UpdateRequest $request, $prId, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $prId;
        $grn = $this->grns->updateFromPr($grnId, $inputs);
        if ($grn) {
            $message = 'GRN is successfully updated.';
            return redirect()->route('approved.purchase.requests.grns.edit',[$prId, $grnId])
                ->withSuccessMessage($message);
        }
        return redirect()->back()->withInput()
            ->withWarningMessage('GRN can not be updated. Received quantity can not be zero and can not exceed total order quantity for a item.');
    }

    public function poGrnIndex(Request $request, $id)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $poIds = $this->purchaseOrders->whereHas('purchaseRequests', function ($q) use ($id) {
                $q->where('pr_id', $id);
            })->select('id')->pluck('id')->toArray();

            $data = $this->grns->where('grnable_type', '=', PurchaseOrder::class)
                ->whereIn('grnable_id', $poIds);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('received_date', function ($row) {
                    return $row->getReceivedDate();
                })->addColumn('request_number', function ($row) {
                    return $row->grnable->getPurchaseOrderNumber();
                })->addColumn('supplier', function ($row) {
                    return $row->getSupplierName();
                })->addColumn('grn_number', function ($row) {
                    return $row->getGrnNumber();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) use ($authUser, $id) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('grns.show', $row->id) . '" rel="tooltip" title="View GRN"><i class="bi bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('grns.edit', $row->id) . '" rel="tooltip" title="Edit GRN"><i class="bi-pencil-square"></i></a>';
                    }
                    // if ($authUser->can('delete', $row)) {
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    //     $btn .= 'data-href="' . route('grns.destroy', $row->id) . '" rel="tooltip" title="Delete GRN">';
                    //     $btn .= '<i class="bi-trash"></i></a>';
                    // }
                    return $btn;
                })
                ->rawColumns(['action', 'grn_number', 'supplier', 'status'])
                ->make(true);
        }
    }
}
