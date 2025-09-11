<?php

namespace Modules\PurchaseRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmitted;
use Modules\PurchaseRequest\Notifications\PurchaseRequestSubmittedApprove;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\PurchaseRequest\Requests\ForwardRequest;


class PurchaseRequestForwardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param PurchaseRequestRepository $purchaseRequests
     * @param UserRepository $users
     */
    public function __construct(
        DistrictRepository        $districts,
        EmployeeRepository        $employees,
        FiscalYearRepository      $fiscalYears,
        protected PurchaseRequestRepository $purchaseRequests,
        UserRepository            $users
    )
    {
        $this->districts = $districts;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->users = $users;
        $this->destinationPath = 'purchaserequest';
    }

    /**
     * Show the form for submitting the specified purchase request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('update', $purchaseRequest);

        // $reviewers = [];
        // $verifiers = [];
        // if ($purchaseRequest->verificationRequired()) {
            $verifiers = $this->users->permissionBasedUsers('budget-verify-purchase-request');
            $reviewers = $this->users->permissionBasedUsers('finance-review-purchase-request');
        // }
        $approvers = $this->users->getSupervisors($purchaseRequest->requester);

        return view('PurchaseRequest::Forward.create')
            ->withAuthUser(auth()->user())
            ->withPurchaseRequest($purchaseRequest)
            ->withReviewers($reviewers)
            ->withVerifiers($verifiers)
            ->withApprovers($approvers);
    }

    /**
     * Update the specified purchase request in storage.
     *
     * @param ForwardRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(ForwardRequest $request, $id)
    {
        $purchaseRequest = $this->purchaseRequests->find($id);
        $this->authorize('update', $purchaseRequest);
        $inputs = $request->validated();

        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                                        ->where('end_date', '>=', date('Y-m-d'))
                                        ->first();
        $inputs['fiscal_year_id'] = $fiscalYear->id;
        $inputs['request_date'] = date('Y-m-d');
        $inputs['log_remarks'] = 'Purchase request is submitted.';
        $inputs['updated_by'] = $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        // $inputs['status_id'] = $purchaseRequest->verificationRequired() ? config('constant.SUBMITTED_STATUS') : config('constant.VERIFIED_STATUS');
        $inputs['status_id'] =config('constant.SUBMITTED_STATUS');

        $purchaseRequest = $this->purchaseRequests->forward($id, $inputs);
        if ($purchaseRequest) {
            $message = 'Purchase request is successfully updated.';
            if ($purchaseRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Purchase request is successfully submitted for verification.';
                // $purchaseRequest->reviewer->notify(new PurchaseRequestSubmitted($purchaseRequest));
                $purchaseRequest->budgetVerifier->notify(new PurchaseRequestSubmitted($purchaseRequest));
            } else if ($purchaseRequest->status_id == config('constant.VERIFIED_STATUS')) {
                $message = 'Purchase request is successfully submitted for approval.';
                $purchaseRequest->approver->notify(new PurchaseRequestSubmittedApprove($purchaseRequest));
            }
            return response()->json(['status' => 'ok',
                'purchaseRequest' => $purchaseRequest,
                'message' => $message], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase Request can not be submitted.'], 422);
    }
}
