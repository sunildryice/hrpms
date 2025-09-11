<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestApproved;
use Modules\PurchaseRequest\Notifications\PurchaseRequestProcurementApproved;
use Modules\PurchaseRequest\Notifications\PurchaseRequestRecommended;
use Modules\PurchaseRequest\Notifications\PurchaseRequestRejected;
use Modules\PurchaseRequest\Notifications\PurchaseRequestReturned;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PurchaseRequest\Requests\Approve\StoreRequest;
use DataTables;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmittedApprove;

class ApproveController extends Controller
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
        protected EmployeeRepository        $employees,
        protected FiscalYearRepository      $fiscalYears,
        protected PurchaseRequestRepository $purchaseRequests,
        protected UserRepository            $users
    )
    {
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
                ->where('status_id',config('constant.VERIFIED_STATUS'))
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
                    $btn .= route('approve.purchase.requests.create', $row->id) . '" rel="tooltip" title="Approve Purchase Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('PurchaseRequest::Approve.index');
    }

    public function create($purchaseRequestId)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('approve', $purchaseRequest);

        $reviewers = $this->users->permissionBasedUsers('review-recommended-purchase-request');
        $approvers = $this->users->permissionBasedUsers('approve-recommended-purchase-request');

        return view('PurchaseRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withPurchaseRequest($purchaseRequest)
            ->withReviewers($reviewers)
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $purchaseRequestId)
    {
        $purchaseRequest = $this->purchaseRequests->find($purchaseRequestId);
        $this->authorize('approve', $purchaseRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')  && !isset($inputs['verifier_id'])){
            $inputs['status_id'] = config('constant.RECOMMENDED2_STATUS');
        }
        $purchaseRequest = $this->purchaseRequests->approve($purchaseRequest->id, $inputs);
        $officers = $purchaseRequest->procurementOfficers;
        if ($purchaseRequest) {
            $message = '';
            if ($purchaseRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Purchase request is successfully returned.';
                $purchaseRequest->requester->notify(new PurchaseRequestReturned($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Purchase request is successfully rejected.';
                $purchaseRequest->requester->notify(new PurchaseRequestRejected($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Purchase request is successfully recommended.';
                $purchaseRequest->verifier->notify(new PurchaseRequestRecommended($purchaseRequest));
            }else if($purchaseRequest->status_id == config('constant.RECOMMENDED2_STATUS')){
                $message = 'Purchase request is successfully recommended for approval.';
                $purchaseRequest->approver->notify(new PurchaseRequestRecommended($purchaseRequest));
            }
             else {
                $message = 'Purchase request is successfully approved.';
                $purchaseRequest->requester->notify(new PurchaseRequestApproved($purchaseRequest));
                foreach ($officers as $officer) {
                    $officer->notify(new PurchaseRequestProcurementApproved($purchaseRequest));
                }

            }

            return redirect()->route('approve.purchase.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Purchase request can not be approved.');
    }
}
