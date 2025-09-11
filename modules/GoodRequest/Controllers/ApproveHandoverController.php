<?php

namespace Modules\GoodRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\GoodRequest\Notifications\HandoverApproved;
use Modules\GoodRequest\Notifications\HandoverReturned;
use Modules\GoodRequest\Repositories\GoodRequestAssetRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\GoodRequest\Requests\Handover\Approve\StoreRequest;

class ApproveHandoverController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected GoodRequestRepository $goodRequests,
        protected GoodRequestAssetRepository $goodRequestAssets
    ) {}

    /**
     * Display a listing of the good requests
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequestAssets->with(['asset'])->select(['*'])
                ->where(function ($q) use ($authUser) {
                    $q->where('reviewer_id', $authUser->id);
                    $q->where('handover_status_id', config('constant.SUBMITTED_STATUS'));
                })->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->where('handover_status_id', config('constant.RECOMMENDED_STATUS'));
                })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('asset_number', function ($row) {
                    return $row->asset->getAssetNumber();
                })->addColumn('item_name', function ($row) {
                    return $row->asset->inventoryItem->getItemName();
                })->addColumn('condition', function ($row) {
                    return $row->getCondition();
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.asset.handovers.create', $row->id).'" rel="tooltip" title="Approve Asset Handover">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::Handover.Approve.index');
    }

    public function create($goodRequestAssetId)
    {
        $authUser = auth()->user();
        $goodRequestAsset = $this->goodRequestAssets->find($goodRequestAssetId);
        $this->authorize('approve', $goodRequestAsset);

        return view('GoodRequest::Handover.Approve.create')
            ->withAuthUser($authUser)
            ->withGoodRequestAsset($goodRequestAsset)
            ->withGoodRequest($goodRequestAsset->goodRequest);
    }

    public function store(StoreRequest $request, $goodRequestAssetId)
    {
        $goodRequestAsset = $this->goodRequestAssets->find($goodRequestAssetId);
        $this->authorize('approve', $goodRequestAsset);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequestAsset = $this->goodRequestAssets->approve($goodRequestAsset->id, $inputs);

        if ($goodRequestAsset) {
            $message = '';
            if ($goodRequestAsset->handover_status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Asset handover is successfully returned.';
                $goodRequestAsset->requester->notify(new HandoverReturned($goodRequestAsset));
            } elseif ($goodRequestAsset->handover_status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Asset handover is successfully approved.';
                $goodRequestAsset->requester->notify(new HandoverApproved($goodRequestAsset));
            }

            return redirect()->route('approve.asset.handovers.index')->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Asset handover can not be approved.');
    }
}
