<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\GoodRequest\Requests\Handover\StoreRequest;
use Modules\GoodRequest\Notifications\HandoverSubmitted;

class HandoverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GoodRequestRepository $goodRequests
     * @param GoodRequestAssetRepository $goodRequestAssets
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository    $employees,
        protected FiscalYearRepository  $fiscalYears,
        protected GoodRequestRepository $goodRequests,
        protected GoodRequestAssetRepository $goodRequestAssets,
        protected UserRepository        $users
    )
    {
    }

    public function create($goodAssetId)
    {
        $authUser = auth()->user();
        $goodRequestAsset = $this->goodRequestAssets->find($goodAssetId);
        $this->authorize('handover', $goodRequestAsset);
        $approvers = $this->users->permissionBasedUsers('approve-asset-handover');

        return view('GoodRequest::Handover.create')
            ->withAuthUser($authUser)
            ->withGoodRequest($goodRequestAsset->goodRequest)
            ->withGoodRequestAsset($goodRequestAsset)
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $goodAssetId)
    {
        $goodRequestAsset = $this->goodRequestAssets->find($goodAssetId);
        $this->authorize('handover', $goodRequestAsset);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequestAsset = $this->goodRequestAssets->update($goodRequestAsset->id, $inputs);

        if ($goodRequestAsset) {
            $message = '';
            if ($goodRequestAsset->handover_status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Asset is successfully submitted for handover.';
                $goodRequestAsset->approver->notify(new HandoverSubmitted($goodRequestAsset));
            }
            return redirect()->route('profile.assets.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Asset can not be submitted for handover.');
    }
}
