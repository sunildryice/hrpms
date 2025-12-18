<?php

namespace Modules\AssetDisposition\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AssetDisposition\Models\DispositionRequestAsset;
use Modules\AssetDisposition\Notifications\AssetDispositionSubmitted;
use Modules\AssetDisposition\Repositories\DispositionRequestRepository;
use Modules\AssetDisposition\Requests\StoreRequest;
use Modules\AssetDisposition\Requests\UpdateRequest;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Master\Repositories\DispositionTypeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\Facades\DataTables;

class DispositionRequestController extends Controller
{
    private $user;

    private $dispositionRequests;

    public function __construct(
        DispositionRequestRepository $dispositionRequests,
        protected DispositionTypeRepository $dispositionTypes,
        protected AssetRepository $assets,
        protected OfficeRepository $offices,
        UserRepository $user,
        protected DispositionRequestAsset $dispositionRequestAssets
    ) {
        $this->dispositionRequests = $dispositionRequests;
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $inputs = $this->dispositionRequests->with(['requester', 'logs', 'office', 'disposeAssets.asset'])
                ->where(function ($q) use ($authUser) {
                    $q->where('requester_id', $authUser->id);
                })->orWhereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                    $q->orWhere('original_user_id', $authUser->id);
                })->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($inputs)
                ->addIndexColumn()
                ->addColumn('office_name', function ($row) {
                    return $row->office->office_name;
                })
                ->addColumn('assets', function ($row) {
                    return implode(',<br> ', $row->getDisposedAssetCodes());
                })
                ->addColumn('disposition_type', function ($row) {
                    return $row->getDispositionType();
                })->addColumn('disposition_date', function ($row) {
                    return $row->getDispositionDate();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {

                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('asset.disposition.show', $row->id) . '" rel="tooltip" title="View Asset Disposition"><i class="bi-eye"></i></a>';
                    if ($authUser->can('update', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('asset.disposition.edit', $row->id) . '" rel="tooltip" title="Edit Disposition Request"><i class="bi-pencil-square"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('asset.disposition.destroy', $row->id) . '"  rel="tooltip" title="Delete Asset Disposition">';
                        $btn .= '<i class="bi-trash3"></i></a>';
                    }

                    return $btn;
                })->rawColumns(['action', 'status', 'assets'])
                ->make(true);
        }

        return view('AssetDisposition::index');
    }

    public function create()
    {
        $authUser = auth()->user();
        $approvers = $this->user->permissionBasedUsers('approve-asset-disposition');
        $dispositionTypes = $this->dispositionTypes->getDispositionTypes();
        $offices = $this->offices->getActiveOffices();

        return view('AssetDisposition::create')
            ->with([
                'dispositionTypes' => ($dispositionTypes),
                'offices' => ($offices),
                'approvers' => ($approvers),
            ]);
    }

    public function store(StoreRequest $request)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['status_id'] = config('constant.CREATED_STATUS');
        $dispositionRequest = $this->dispositionRequests->create($inputs);
        if ($dispositionRequest) {
            $message = 'Asset Disposition Request is successfully created.';

            return redirect()->route('asset.disposition.edit', $dispositionRequest->id)
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Asset Disposition Request can not be added');
    }

    public function edit($id)
    {
        $dispositionRequest = $this->dispositionRequests->find($id);
        $this->authorize('update', $dispositionRequest);
        $approvers = $this->user->permissionBasedUsers('approve-asset-disposition');
        $dispositionTypes = $this->dispositionTypes->getDispositionTypes();
        $disposeAssets = $this->dispositionRequestAssets->where('disposition_request_id', $id)
            ->orderby('id', 'asc')
            ->get();
        $disposeAssetIds = $disposeAssets->pluck('asset_id')->toArray();
        $assets = $this->assets->getDisposableAssets($dispositionRequest->office_id);
        $offices = $this->offices->getActiveOffices();

        return view('AssetDisposition::edit')
            ->with([
                'dispositionTypes' => ($dispositionTypes),
                'assets' => ($assets),
                'dispositionRequest' => ($dispositionRequest),
                'offices' => ($offices),
                'disposeAssets' => ($disposeAssets),
                'approvers' => ($approvers),
            ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $authUser = auth()->user();
        $dispositionRequest = $this->dispositionRequests->find($id);
        $this->authorize('update', $dispositionRequest);
        $authUser = auth()->user();
        $inputs = $request->validated();

        foreach ($inputs['asset_dispose']['asset'] as $index => $asset) {
            $assetInputs = [
                'asset_id' => $asset,
                'disposition_reason' => $inputs['asset_dispose']['reason'][$index],
            ];
            if (count(array_filter($assetInputs))) {
                $inputs['asset_input'][$index] = $assetInputs;
            }
        }

        // dd($this->assets->find($inputs['asset_input'][0]['asset_id'])->inventoryItem->office);
        // incorrect office case
        if (isset($inputs['asset_input'])) {
            foreach ($inputs['asset_input'] as $assetInput) {
                $inputAsset = $this->assets->find($assetInput['asset_id']);
                if ($inputAsset->assigned_office_id != $inputs['office_id'] && $inputAsset->inventoryItem->office_id != $inputs['office_id']) {
                    return redirect()->back()->withInput()->withWarningMessage('Asset office is not same as disposition office.');
                }
            }
        }

        $inputs['updated_by'] = auth()->id();
        $inputs['requester_id'] = $inputs['created_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $dispositionRequest = $this->dispositionRequests->update($id, $inputs);
        if ($dispositionRequest) {
            $message = 'Asset Disposition Request is successfully updated.';
            if ($dispositionRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $message = 'Asset Disposition Request is successfully submitted.';
                $dispositionRequest->approver->notify(new AssetDispositionSubmitted($dispositionRequest));
            } elseif ($dispositionRequest->status_id == config('constant.CREATED_STATUS')) {
                $message = 'Asset Disposition Request is successfully updated.';

                return redirect()->back()->withInput()->withSuccessMessage($message);
            }

            return redirect()->route('asset.disposition.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Asset Disposition Request can not be updated');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $dispositionRequest = $this->dispositionRequests->find($id);

        return view('AssetDisposition::show')
            ->with([
                'authUser' => ($authUser),
                'dispositionRequest' => ($dispositionRequest),
            ]);
    }

    public function destroy($id)
    {
        $dispositionRequest = $this->dispositionRequests->find($id);
        $this->authorize('delete', $dispositionRequest);
        $flag = $this->dispositionRequests->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Asset Disposition Request is deleted successfully',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Asset Disposition Request can not be deleted,',
        ], 422);
    }

    public function cancel($id)
    {
        $dispositionRequest = $this->dispositionRequests->find($id);
        // $this->authorize('cancel', $dispositionRequest);
        $inputs = [
            'user_id' => auth()->id(),
            'log_remarks' => 'Asset Disposition Request is cacelled',
            'original_user_id' => session()->has('original_user') ? session()->get('original_user') : null,
        ];
        $dispositionRequest = $this->dispositionRequests->cancel($id, $inputs);
        if ($dispositionRequest) {
            if ($dispositionRequest->status_id == config('constant.CANCELLED_STATUS')) {
                if ($dispositionRequest->reviewer_id) {
                    $dispositionRequest->reviewer->notify(new AssetDispositionCancelled($dispositionRequest));
                }
                if ($dispositionRequest->approver_id && $dispositionRequest->reviewer_id != $dispositionRequest->approver_id) {
                    $dispositionRequest->approver->notify(new AssetDispositionCancelled($dispositionRequest));
                }
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Asset Disposition is cancelled successfully',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Asset Disposition can no be cancelled',
        ], 422);
    }
}
