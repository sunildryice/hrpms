<?php

namespace Modules\GoodRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\GoodRequest\Notifications\DirectAssign\DirectAssignApproved;
use Modules\GoodRequest\Notifications\DirectAssign\DirectAssignRejected;
use Modules\GoodRequest\Notifications\DirectAssign\DirectAssignSubmitted;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\GoodRequest\Notifications\DirectAssign\AssetAssigned;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Requests\Assign\Direct\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;

class DirectAssignController extends Controller
{
    protected $assets;
    protected $employees;
    protected $goodRequests;
    /**
     * Create a new controller instance.
     *
     * @param AssetRepository $assets
     */
    public function __construct(
        AssetRepository $assets,
        GoodRequestRepository $goodRequests,
        EmployeeRepository $employees,
        UserRepository $users
    )
    {
        $this->assets = $assets;
        $this->employees = $employees;
        $this->goodRequests = $goodRequests;
        $this->users = $users;
    }


    public function create($id)
    {
        $asset = $this->assets->find($id);
        $employees = $this->employees->getActiveEmployees();
        $approvers = $this->users->permissionBasedUsers('approve-direct-dispatch-good-request');
        return view('GoodRequest::Assign.Direct.create', compact('asset', 'employees', 'approvers'));
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $asset = $this->assets->find($id);
        abort_if(isset($asset->assigned_user_id), 403);
        $inputs['created_by'] = auth()->user()->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->storeDirectAssign($asset->id, $inputs);
        if($goodRequest){
            $goodRequest->approver->notify(new DirectAssignSubmitted($goodRequest));
            return response()->json([
                'status' => 'ok',
                'message'   => 'Asset assigned successfully,',
            ],200);
        }
        return response()->json([
            'status' => 'error',
            'message'   => 'Failed to assign asset.',
        ],422);
    }

    public function indexApprove(Request $request)
    {
        $authUser = auth()->user();
        if($request->ajax()){
            $data = $this->goodRequests->where('is_direct_assign', '=', '1')
            ->where('approver_id', '=', $authUser->id)
            ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
            ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('request_number', function ($row) {
                return $row->getGoodRequestNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '';
                if ($authUser->can('approveDirectAssignRequest', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.good.requests.direct.assign.create', $row->id) . '" rel="tooltip" title="Approve Direct Assign Request"><i class="bi-pencil-square"></i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('GoodRequest::Assign.Direct.Approve.index');
    }

    public function createApprove($id)
    {
        $goodRequest = $this->goodRequests->find($id);
        $goodRequestAsset = $goodRequest->goodRequestAssets()->first();

        $this->authorize('approveDirectAssignRequest', $goodRequest);

        return view('GoodRequest::Assign.Direct.Approve.create', compact('goodRequest', 'goodRequestAsset'));
    }

    public function storeApprove(Request $request, $id)
    {
        $inputs = $request->validate([
            'status_id' => 'required',
            'log_remarks'   => 'required',
        ]);
        $goodRequest = $this->goodRequests->find($id);
        $this->authorize('approveDirectAssignRequest', $goodRequest);
        $inputs['updated_by'] = auth()->user()->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $goodRequest = $this->goodRequests->approveDirectAssign($goodRequest->id, $inputs);

        if ($goodRequest) {
            $message = '';
            if ($goodRequest->status_id == config('constant.ASSIGNED_STATUS')) {
                $message = 'Asset Direct Assigned Successfully.';
                $goodRequest->requester->notify(new DirectAssignApproved($goodRequest));
                $goodRequest->receiver->notify(new AssetAssigned($goodRequest));
            } elseif ($goodRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Direct Assete Assign request rejected successfully.';
                $goodRequest->requester->notify(new DirectAssignRejected($goodRequest));
            }

            return redirect()->route('approve.good.requests.direct.assign.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withWarningMessage('Direct Asset Assign request could not be processed.');
    }

}
