<?php

namespace Modules\GoodRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\DirectAssign\DirectAssignReceived;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\GoodRequest\Requests\Assign\Receive\StoreRequest;
use Modules\Inventory\Repositories\AssetRepository;
use Modules\Privilege\Repositories\UserRepository;

class DirectAssignReceiveController extends Controller
{
    protected $assets;

    protected $employees;

    protected $goodRequests;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        AssetRepository $assets,
        GoodRequestRepository $goodRequests,
        EmployeeRepository $employees,
        UserRepository $users
    ) {
        $this->assets = $assets;
        $this->employees = $employees;
        $this->goodRequests = $goodRequests;
        $this->users = $users;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->query()
                ->where(function ($q) {
                    $q->where('is_direct_assign', '=', '1');
                    $q->orWhere('is_direct_dispatch', '=', '1');
                })
                ->where('receiver_id', '=', $authUser->id)
                ->whereIn('status_id', [config('constant.ASSIGNED_STATUS'), config('constant.APPROVED_STATUS')])
                ->whereNull('received_at')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('approver', function ($row) {
                    return $row->getApproverName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('receiveDirectAssignRequest', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('receive.good.requests.direct.assign.create', $row->id).'" rel="tooltip" title="Receive Confirmation"><i class="bi-pencil-square"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::Assign.Direct.Receive.index');
    }

    public function create($id)
    {
        $goodRequest = $this->goodRequests->find($id);
        $goodRequestAssets = $goodRequest->goodRequestAssets;
        $this->authorize('receiveDirectAssignRequest', $goodRequest);

        return view('GoodRequest::Assign.Direct.Receive.create', compact('goodRequest', 'goodRequestAssets'));
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $goodRequest = $this->goodRequests->find($id);
        $this->authorize('receiveDirectAssignRequest', $goodRequest);
        $inputs['created_by'] = auth()->user()->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->receive($goodRequest->id, $inputs);
        if ($goodRequest) {
            $message = 'Aseset Received Successfully.';

            if (isset($goodRequest->logistic_officer_id)) {
                $goodRequest->logisticOfficer->notify(new DirectAssignReceived($goodRequest));
            } else {
                $goodRequest->createdBy->notify(new DirectAssignReceived($goodRequest));
            }

            return redirect()->route('receive.good.requests.direct.assign.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withWarningMessage('Asset receive could not be confirmed.');
    }
}
