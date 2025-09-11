<?php

namespace Modules\GoodRequest\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\GoodRequest\Notifications\DirectDispatch\DirectDispatchApproved;
use Modules\GoodRequest\Notifications\DirectDispatch\DirectDispatchRejected;
use Modules\GoodRequest\Notifications\DirectDispatch\DirectDispatchSubmitted;
use Modules\GoodRequest\Notifications\DirectDispatch\ItemDispatched;
use Modules\GoodRequest\Repositories\GoodRequestItemRepository;
use Modules\GoodRequest\Repositories\GoodRequestRepository;
use Modules\GoodRequest\Requests\DirectDispatch\Bulk\StoreRequest as StoreBulkRequest;
use Modules\GoodRequest\Requests\DirectDispatch\StoreRequest;
use Modules\GoodRequest\Requests\DirectDispatch\UpdateRequest;
use Modules\Inventory\Models\InventoryItem;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class DirectDispatchController extends Controller
{
    private $goodRequests;

    private $inventories;

    private $offices;

    private $users;

    private $employees;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        GoodRequestRepository $goodRequests,
        GoodRequestItemRepository $goodRequestItems,
        Helper $helper,
        ProjectCodeRepository $projectCodes,
        UserRepository $users,
        InventoryItem $inventories,
        OfficeRepository $offices
    ) {
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->goodRequests = $goodRequests;
        $this->goodRequestItems = $goodRequestItems;
        $this->helper = $helper;
        $this->projectCodes = $projectCodes;
        $this->users = $users;
        $this->destinationPath = 'goodRequest';
        $this->inventories = $inventories;
        $this->offices = $offices;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->with(['requester', 'status', 'fiscalYear'])
                ->where('is_direct_dispatch', '1')
                ->where('created_by', $authUser->id)
                ->orderBy('fiscal_year_id', 'desc')
                ->orderBy('good_request_number', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('good.requests.direct.dispatch.show', $row->id).'" rel="tooltip" title="View Direct Dispatch Request"><i class="bi bi-eye"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::DirectDispatch.index');
    }

    /**
     * Show the form for creating direct dispatch of consumables of office use.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($inventoryItem)
    {
        $authUser = auth()->user();
        $inventoryItem = $this->inventories->find($inventoryItem);
        $offices = $this->offices->getActiveOffices();
        $approvers = $this->users->permissionBasedUsers('approve-direct-dispatch-good-request');

        return view('GoodRequest::DirectDispatch.create')
            ->withApprovers($approvers)
            ->withOffices($offices)
            ->withInventoryItem($inventoryItem);
    }

    /**
     * Store a newly created direct dispatch in storage.
     *
     * @return mixed
     */
    public function store(StoreRequest $request, $inventoryItem)
    {
        $authUser = auth()->user();
        $inputs = $request->validated();

        $inputs['created_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['assigned_inventory_item_id'] = $inventoryItem;
        $goodRequest = $this->goodRequests->storeDirectDispatch($inputs);
        if ($goodRequest) {
            $goodRequest->approver->notify(new DirectDispatchSubmitted($goodRequest));

            return response()->json([
                'status' => 'ok',
                'message' => 'Direct dispatch request sent successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Direct dispatch request could not be sent.',
        ], 422);
    }

    public function show($goodRequest)
    {
        $goodRequest = $this->goodRequests->find($goodRequest);

        return view('GoodRequest::DirectDispatch.show')
            ->withGoodRequest($goodRequest);
    }

    public function indexApprove(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->goodRequests->where('is_direct_dispatch', '=', '1')
                ->where('approver_id', '=', auth()->user()->id)
                ->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getGoodRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('approveDirectDispatchRequest', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('good.requests.direct.dispatch.approve.create', $row->id).'" rel="tooltip" title="Assign Direct Dispatch Request"><i class="bi-pencil-square"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('GoodRequest::DirectDispatch.Approve.index');
    }

    public function createApprove($goodRequest)
    {
        $goodRequest = $this->goodRequests->find($goodRequest);
        $this->authorize('approveDirectDispatchRequest', $goodRequest);

        return view('GoodRequest::DirectDispatch.Approve.create')
            ->withGoodRequest($goodRequest);
    }

    public function storeApprove(UpdateRequest $request, $goodRequest)
    {
        $goodRequest = $this->goodRequests->find($goodRequest);
        $this->authorize('approveDirectDispatchRequest', $goodRequest);

        $inputs = $request->validated();
        // $goodRequestItem = $goodRequest->latestGoodRequestItem;

        $inputs['created_by'] = auth()->user()->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;

        $goodRequest = $this->goodRequests->approveDirectDispatch($goodRequest->id, $inputs);

        if ($goodRequest) {
            $message = 'Direct dispatch request processed successfully.';
            if ($goodRequest->status_id == config('constant.APPROVED_STATUS')) {
                $message = 'Direct dispatch request approved successfully.';
                $goodRequest->requester->notify(new DirectDispatchApproved($goodRequest));
                $goodRequest->receiver->notify(new ItemDispatched($goodRequest));
            } elseif ($goodRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Direct dispatch request rejected successfully.';
                $goodRequest->requester->notify(new DirectDispatchRejected($goodRequest));
            }

            return redirect()->route('good.requests.direct.dispatch.approve.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()->withWarningMessage('Direct dispatch request could not be processed.');
    }

    public function createBulk()
    {
        $inventoryItems = $this->inventories->select(['id', 'item_id', 'unit_id', 'item_name', 'batch_number', 'quantity', 'assigned_quantity', 'distribution_type_id'])
            ->with([
                'unit' => function ($q) {
                    $q->select(['id', 'title']);
                },
            ])->whereHas('distributionType', function ($q) {
                $q->where('title', 'office use');
            })->whereHas('item', function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('inventory_type_id', 3);
                });
            })->whereColumn('quantity', '>', 'assigned_quantity')->get();
        $offices = $this->offices->getActiveOffices();
        $approvers = $this->users->permissionBasedUsers('approve-direct-dispatch-good-request');
        $employees = $this->employees->getActiveEmployees();

        return view('GoodRequest::DirectDispatch.Bulk.create', compact('inventoryItems', 'offices', 'approvers', 'employees'));
    }

    public function storeBulk(StoreBulkRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = $inputs['updated_by'] = $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        // $this->authorize
        $goodRequest = $this->goodRequests->storeDispatchBulk($inputs);
        if ($goodRequest) {
            $message = 'Direct Dispatch request is added.';
            if ($goodRequest->status_id == config('constant.SUBMITTED_STATUS')) {
                $goodRequest->approver->notify(new DirectDispatchSubmitted($goodRequest));
            }

            return redirect()->route('good.requests.direct.dispatch.index')->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withErrorMessage('Direct Dispatch request could not be added.');
    }
}
