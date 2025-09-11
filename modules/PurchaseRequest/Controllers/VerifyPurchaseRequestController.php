<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestRejected;
use Modules\PurchaseRequest\Notifications\PurchaseRequestReturned;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmitted;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmittedApprove;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmittedVerify;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\Verify\StoreRequest;

class VerifyPurchaseRequestController extends Controller
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
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected PurchaseRequestRepository $purchaseRequests,
        protected UserRepository $users
    ) {
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
                ->where(function ($q) use ($authUser) {
                    $q->where('budget_verifier_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                })->orderBy('required_date', 'desc')
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
                $btn .= route('verify.purchase.requests.create', $row->id) . '" rel="tooltip" title="Review Purchase Request">';
                $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseRequest::Verify.index');
    }

    public function create($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('verify', $purchaseRequest);

        $reviewers = $this->users->permissionBasedUsers('finance-review-purchase-request');
        return view('PurchaseRequest::Verify.create')
            ->withAuthUser($authUser)
            ->withReviewers($reviewers)
            ->withVerifyFlag($purchaseRequest->verificationRequired())
            ->withPurchaseRequest($purchaseRequest);
    }

    public function store(StoreRequest $request, $purchaseRequestId)
    {
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('verify', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        // $inputs['status_id'] = $inputs['status_id'] ?: $purchaseRequest->status_id;
        if(!$purchaseRequest->verificationRequired() && $inputs['status_id'] == config('constant.VERIFIED2_STATUS')){
            $inputs['status_id'] = config('constant.VERIFIED_STATUS');
        }
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $purchaseRequest = $this->purchaseRequests->verify($purchaseRequest->id, $inputs);

        if ($purchaseRequest) {
            $message = 'Purchase request is successfully updated.';
            if ($purchaseRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Purchase request is successfully returned.';
                $purchaseRequest->requester->notify(new PurchaseRequestReturned($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Purchase request is successfully rejected.';
                $purchaseRequest->requester->notify(new PurchaseRequestRejected($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.VERIFIED2_STATUS')) {
                $message = 'Purchase request is successfully verified.';
                $purchaseRequest->reviewer->notify(new PurchaseRequestSubmittedVerify($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Purchase request is successfully verified.';
                $purchaseRequest->approver->notify(new PurchaseRequestSubmittedApprove($purchaseRequest));
            }

            if ($inputs['btn'] == 'submit') {
                return redirect()->route('verify.purchase.requests.index')
                    ->withSuccessMessage($message);
            } else {
                return redirect()->route('verify.purchase.requests.create', $purchaseRequest->id)
                    ->withSuccessMessage($message);
            }
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Purchase request can not be verified.');
    }
}
