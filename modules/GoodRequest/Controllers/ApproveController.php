<?php

namespace Modules\GoodRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\GoodRequestApproved;
use Modules\GoodRequest\Notifications\GoodRequestRejected;
use Modules\GoodRequest\Notifications\GoodRequestReturned;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\Inventory\Repositories\InventoryItemRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\ItemRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\GoodRequest\Requests\Approve\StoreRequest;
use DataTables;


class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param GoodRequestRepository $goodRequests
     * @param InventoryItemRepository $inventoryItems
     * @param ItemRepository $items
     * @param UserRepository $users
     */
    public function __construct(
        protected EmployeeRepository      $employees,
        protected FiscalYearRepository    $fiscalYears,
        protected GoodRequestRepository   $goodRequests,
        protected InventoryItemRepository $inventoryItems,
        protected ItemRepository          $items,
        protected UserRepository          $users
    )
    {
    }

    /**
     * Display a listing of the good requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->with(['status', 'requester'])
                ->where(function ($q) use ($authUser) {
                    $q->where(function ($q) use($authUser) {
                        $q->where('approver_id', $authUser->id)
                        ->where('status_id', config('constant.VERIFIED_STATUS'))
                        ->whereNotNull('reviewer_id');
                    });
                    $q->orWhere(function($q) use($authUser) {
                        $q->where('approver_id', $authUser->id)
                        ->where('status_id', config('constant.SUBMITTED_STATUS'))
                        ->whereNull('reviewer_id');
                    });
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.good.requests.create', $row->id) . '" rel="tooltip" title="Approve good Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::Approve.index');
    }

    public function create($goodRequestId)
    {
        $authUser = auth()->user();
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('approve', $goodRequest);

        $logisticOfficers = $this->users->permissionBasedUsers('assign-good-request');

        return view('GoodRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withGoodRequest($goodRequest)
            ->withLogisticOfficers($logisticOfficers);
    }

    /**
     *  Store a newly assigned asset items to good request in storage.
     *
     * @param StoreRequest $request
     * @param $goodRequestId
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function store(StoreRequest $request, $goodRequestId)
    {
        $goodRequest = $this->goodRequests->find($goodRequestId);
        $this->authorize('approve', $goodRequest);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $goodRequest = $this->goodRequests->approve($goodRequest->id, $inputs);

        if ($goodRequest) {
            $message = '';
            if ($goodRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Good request is successfully returned.';
                $goodRequest->requester->notify(new GoodRequestReturned($goodRequest));
            } else if ($goodRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Good request is successfully rejected.';
                $goodRequest->requester->notify(new GoodRequestRejected($goodRequest));
            } else if ($goodRequest->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Good request is successfully approved.';
                $goodRequest->logisticOfficer->notify(new GoodRequestApproved($goodRequest));
            }

            return redirect()->route('approve.good.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Good request can not be approved.');
    }
}
