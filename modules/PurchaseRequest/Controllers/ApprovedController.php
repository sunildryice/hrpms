<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderItemRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestClosed;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ApprovedController extends Controller
{
    private $employees;
    private $fiscalYears;
    private $purchaseRequests;
    private $purchaseOrderItems;
    private $users;
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseRequestRepository $purchaseRequests
     * @param PurchaseOrderItemRepository $purchaseOrderItems
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseOrderItemRepository  $purchaseOrderItems,
        UserRepository            $users
    )
    {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseRequests = $purchaseRequests;
        $this->purchaseOrderItems = $purchaseOrderItems;
        $this->users = $users;
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
            $data = $this->purchaseRequests->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('required_date', function ($row) {
                    return $row->getRequiredDate();
                })->addColumn('request_date', function ($row) {
                    return $row->getRequestDate();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('purchase_number', function ($row) {
                    return $row->getPurchaseRequestNumber();
                })->addColumn('estimated_amount', function ($row) {
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.purchase.requests.show', $row->id) . '" rel="tooltip" title="View Purchase Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" ';
                    $btn .= 'href="' . route('purchase.requests.print', $row->id) . '" rel="tooltip" title="Print Purchase Request"">';
                    $btn .= '<i class="bi-printer"></i></a>';
                    $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" ';
                    $btn .= 'href="' . route('purchase.requests.items.show', $row->id) . '" rel="tooltip" title="Items"">';
                    $btn .= '<i class="bi bi-diagram-2"></i></a>';

                    // if ($authUser->can('amend', $row)) {
                    //     $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm amend-purchase-request"';
                    //     $btn .= 'data-href = "' . route('purchase.requests.amend.store', $row->id) . '" title="Amend Purchase Request">';
                    //     $btn .= '<i class="bi bi-bootstrap-reboot" ></i></a>';
                    // }
                    if($authUser->can('close', $row)){
                        $btn .= '&emsp;<a class="btn btn-danger btn-sm close-purchase-modal-form" href="';
                        $btn .= route('close.purchase.requests.create', $row->id) . '" rel="tooltip" title="Close"><i class="bi bi-x-circle"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PurchaseRequest::Approved.index');
    }

    /**
     * Show the specified purchase request.
     *
     * @param $purchaseRequestId
     * @return mixed
     */
    public function show($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('viewApproved', $purchaseRequest);

        $remainingItemsCount = $purchaseRequest->purchaseRequestItems()->sum('quantity')
            - $purchaseRequest->purchaseOrderItems()->sum('purchase_order_items.quantity');
        return view('PurchaseRequest::Approved.show')
            ->withRemainingItemsCount($remainingItemsCount)
            ->withPurchaseRequest($purchaseRequest);
    }
}
