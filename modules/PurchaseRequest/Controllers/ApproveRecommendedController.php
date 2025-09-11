<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestApproved;
use Modules\PurchaseRequest\Notifications\PurchaseRequestRecommended;
use Modules\PurchaseRequest\Notifications\PurchaseRequestRejected;
use Modules\PurchaseRequest\Notifications\PurchaseRequestReturned;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\ApproveRecommended\StoreRequest;

class ApproveRecommendedController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseRequestRepository $purchaseRequests
     * @param UserRepository $users
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        PurchaseRequestRepository $purchaseRequests,
        UserRepository $users
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->purchaseRequests = $purchaseRequests;
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
            $data = $this->purchaseRequests->with(['fiscalYear', 'status', 'requester'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->where('status_id', config('constant.RECOMMENDED2_STATUS'))
                ->orderBy('required_date', 'desc')
                ->get();

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
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('approve.recommended.purchase.requests.create', $row->id) . '" rel="tooltip" title="Approve Purchase Request">';
                $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseRequest::ApproveRecommended.index');
    }

    public function create($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('approveRecommended', $purchaseRequest);

        return view('PurchaseRequest::ApproveRecommended.create')
            ->withAuthUser($authUser)
            ->withPurchaseRequest($purchaseRequest);
    }

    public function store(StoreRequest $request, $purchaseRequestId)
    {
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('approveRecommended', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseRequest = $this->purchaseRequests->approve($purchaseRequest->id, $inputs);

        if ($purchaseRequest) {
            $message = '';
            if ($purchaseRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Purchase request is successfully returned.';
                $purchaseRequest->requester->notify(new PurchaseRequestReturned($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Purchase request is successfully rejected.';
                $purchaseRequest->requester->notify(new PurchaseRequestRejected($purchaseRequest));
            }else {
                $message = 'Purchase request is successfully approved.';
                $purchaseRequest->requester->notify(new PurchaseRequestApproved($purchaseRequest));
            }

            return redirect()->route('approve.purchase.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Purchase request can not be approved.');
    }
}
