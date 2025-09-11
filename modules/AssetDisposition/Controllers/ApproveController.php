<?php

namespace Modules\AssetDisposition\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AssetDisposition\Notifications\AssetDispositionApproved;
use Modules\AssetDisposition\Notifications\AssetDispositionRejected;
use Modules\AssetDisposition\Notifications\AssetDispositionReturned;
use Modules\AssetDisposition\Notifications\AssetDispositionSubmitted;
use Modules\AssetDisposition\Repositories\DispositionRequestRepository;
use Modules\AssetDisposition\Requests\Approve\StoreRequest;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\DispositionTypeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\Facades\DataTables;

class ApproveController extends Controller
{
    private $user;

    public function __construct(
        protected DispositionRequestRepository $dispositionRequest,
        protected DispositionTypeRepository $dispositionTypes,
        protected AssetRepository $assets,
        UserRepository $user,
    ) {
        $this->user = $user;

    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $inputs = $this->dispositionRequest->with(['requester', 'status', 'office'])->select('*')
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($inputs)
                ->addIndexColumn()
                ->addColumn('office_name', function ($row) {
                    return $row->office->office_name;
                })
                ->addColumn('disposition_type', function ($row) {
                    return $row->getDispositionType();
                })->addColumn('disposition_date', function ($row) {
                    return $row->getDispositionDate();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.asset.disposition.create', $row->id).'" rel="tooltip" title="Approve Asset Disposition">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AssetDisposition::Approve.index');
    }

    public function create($id)
    {
        $authUser = auth()->user();
        $dispositionRequest = $this->dispositionRequest->find($id);
        $this->authorize('approve', $dispositionRequest);
        $approvers = $this->user->permissionBasedUsers('approve-asset-disposition');
        $recommendApprovers = $this->user->permissionBasedUsers('approve-recommended-asset-disposition');

        return view('AssetDisposition::Approve.create')
            ->withDispositionRequest($dispositionRequest)
            ->withRecommendApprovers($recommendApprovers)
            ->withApprovers($approvers);
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $dispositionRequest = $this->dispositionRequest->find($id);
        $this->authorize('approve', $dispositionRequest);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $dispositionRequest = $this->dispositionRequest->approve($dispositionRequest->id, $inputs);
        if ($dispositionRequest) {
            $message = '';
            if ($dispositionRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Asset Disposition Request is successfully returned';
                $dispositionRequest->requester->notify(new AssetDispositionReturned($dispositionRequest));
            } elseif ($dispositionRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Asset Disposition Request is rejected';
                $dispositionRequest->requester->notify(new AssetDispositionRejected($dispositionRequest));
            } elseif ($dispositionRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Asset Disposition Request is successfully recommended';
                $dispositionRequest->approver->notify(new AssetDispositionSubmitted($dispositionRequest));
            } else {
                $message = 'Asset Disposition Request is successfully approved';
                $dispositionRequest->requester->notify(new AssetDispositionApproved($dispositionRequest));
            }

            return redirect()->route('approve.asset.disposition.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Asset Disposition Request can not be approved.');
    }
}
