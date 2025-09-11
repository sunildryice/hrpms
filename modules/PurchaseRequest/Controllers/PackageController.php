<?php

namespace Modules\PurchaseRequest\Controllers;

use App\Http\Controllers\Controller;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\PackageRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestItemRepository;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\PurchaseRequest\Requests\Item\Package\StoreRequest;

class PackageController extends Controller
{

    public function __construct(
        PurchaseRequestRepository $purchaseRequests,
        PackageRepository $packages,
        PurchaseRequestItemRepository $purchaseRequestItems,
        AccountCodeRepository $accountCodes,
        ActivityCodeRepository $activityCodes,
        DonorCodeRepository $donorCodes,
        OfficeRepository $offices,
    ) {
        $this->packages = $packages;
        $this->purchaseRequests = $purchaseRequests;
        $this->purchaseReqeuqestItems = $purchaseRequestItems;
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->offices = $offices;
    }

    public function add($id)
    {
        $packages = $this->packages->all();

        $purchaseRequest = $this->purchaseRequests->find($id);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        return view('PurchaseRequest::Item.Package.create')
            ->withPackages($packages)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withOffices($this->offices->getActiveOffices())
            ->withPurchaseRequest($purchaseRequest);
    }

    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $purchaseRequest = $this->purchaseRequests->find($id);
        $inputs = $request->validated();
        $inputs['purchase_request_id'] = $purchaseRequest->id;
        $result = $this->purchaseReqeuqestItems->createFromPackage($inputs);
        if ($result) {
            return response()->json(['status' => 'ok',
                'purchaseItemCount' => $result['updatedPR']->purchaseRequestItems()->count(),
                'message' => 'Purchase request item is successfully added.',
                'package' => $result['package']], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Purchase request item can not be added.'], 422);
    }
}
