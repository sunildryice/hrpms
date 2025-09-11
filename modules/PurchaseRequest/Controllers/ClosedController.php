<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderItemRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class ClosedController extends Controller
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
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        PurchaseRequestRepository $purchaseRequests,
        PurchaseOrderItemRepository $purchaseOrderItems,
        UserRepository $users
    ) {
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
            $data = $this->purchaseRequests->getClosed();

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
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('closed.purchase.requests.show', $row->id) . '" rel="tooltip" title="View Purchase Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a target="_blank" class="btn btn-outline-primary btn-sm" ';
                    $btn .= 'href="' . route('purchase.requests.print', $row->id) . '" rel="tooltip" title="Print Purchase Request"">';
                    $btn .= '<i class="bi-printer"></i></a>';
                    $btn .= '&emsp;<a target="_blank" class="btn btn-outline-success btn-sm open-pr" ';
                    $btn .= 'data-href="' . route('open.purchase.requests.store', $row->id) . '" rel="tooltip" data-number="'. $row->getPurchaseRequestNumber() . '"  title="Open PR">';
                    $btn .= '<i class="bi-arrow-clockwise"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('PurchaseRequest::Closed.index');
    }

    public function show($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('viewApproved', $purchaseRequest);

        $prItemArray = $purchaseRequest->purchaseRequestItems()->pluck('id');
        $orderItemCount = $this->purchaseOrderItems->select('id')
            ->whereIn('purchase_request_item_id', $prItemArray)
            ->count();
        return view('PurchaseRequest::Closed.show')
            ->withOrderItemCount($orderItemCount)
            ->withPurchaseRequest($purchaseRequest);
    }
}
