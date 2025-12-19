<?php

namespace Modules\MaintenanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestItemRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;

use Modules\MaintenanceRequest\Requests\Item\StoreRequest;
use Modules\MaintenanceRequest\Requests\Item\UpdateRequest;

use DataTables;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;

class MaintenanceRequestItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param MaintenanceRequestRepository $maintenanceRequests
     * @param MaintenanceRequestItemRepository $maintenanceRequestItems
     * @param DonorCodeRepository $donorCodes
     */
    public function __construct(
        protected ActivityCodeRepository            $activityCodes,
        protected MaintenanceRequestRepository     $maintenanceRequests,
        protected MaintenanceRequestItemRepository $maintenanceRequestItems,
        protected DonorCodeRepository               $donorCodes,
        protected ItemRepository                    $items
    ) {}

    /**
     * Display a listing of the maintenance request items
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $maintenanceRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $maintenanceRequest = $this->maintenanceRequests->find($maintenanceRequestId);
            $data = $this->maintenanceRequestItems->select(['*'])
                ->with(['activityCode', 'accountCode', 'donorCode', 'item'])
                ->whereMaintenanceId($maintenanceRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            $datatable->addColumn('action', function ($row) use ($authUser, $maintenanceRequest) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                $btn .= route('maintenance.requests.items.edit', [$row->maintenance_id, $row->id]) . '" rel="tooltip" title="Edit Maintenance Request Item""><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('maintenance.requests.items.destroy', [$row->maintenance_id, $row->id]) . '" rel="tooltip" title="Delete Maintenance Request Item"">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            });

            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->getDonorCode();
            })->addColumn('item_name', function ($row) {
                return $row->getItemName();
            })->addColumn('replacement_good_needed', function ($row) {
                return $row->replacement_good_needed ? 'Yes' : 'No';
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new maintenance request item.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $maintenanceRequest = $this->maintenanceRequests->find($id);
        $items = $this->items->getActiveItems();

        return view('MaintenanceRequest::Item.create')
            ->withActivityCodes($activityCodes)
            ->withMaintenanceRequest($maintenanceRequest)
            ->withDonorCodes($donorCodes)
            ->withItems($items);
    }

    /**
     * Store a newly created maintenance request item in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $maintenanceRequest = $this->maintenanceRequests->find($id);
        $inputs = $request->validated();
        $inputs['maintenance_id'] = $maintenanceRequest->id;
        $maintenanceRequestItem = $this->maintenanceRequestItems->create($inputs);

        if ($maintenanceRequestItem) {
            return response()->json([
                'status' => 'ok',
                'maintenanceRequest' => $maintenanceRequestItem->maintenanceRequest,
                'maintenanceRequestItem' => $maintenanceRequestItem,
                'estimatedCost' => $maintenanceRequestItem->maintenanceRequest->estimated_cost,
                'message' => 'Maintenance request item is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Maintenance request item can not be added.'
        ], 422);
    }

    /**
     * Show the form for editing the specified maintenance request item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($drId, $id)
    {
        $maintenanceRequest = $this->maintenanceRequests->find($drId);
        $maintenanceRequestItem = $this->maintenanceRequestItems->find($id);
        $this->authorize('update', $maintenanceRequest);

        $accountCodes = $maintenanceRequestItem->activityCode ? $maintenanceRequestItem->activityCode->accountCodes
            ->whereNotNull('activated_at') : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $items = $this->items->getActiveItems();

        return view('MaintenanceRequest::Item.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withItems($items)
            ->withMaintenanceRequestItem($maintenanceRequestItem);
    }

    /**
     * Update the specified maintenance request item in storage.
     *
     * @param UpdateRequest $request
     * @param $drId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $drId, $id)
    {
        $maintenanceRequestItem = $this->maintenanceRequestItems->find($id);
        $this->authorize('update', $maintenanceRequestItem->maintenanceRequest);
        $inputs = $request->validated();
        $maintenanceRequestItem = $this->maintenanceRequestItems->update($id, $inputs);
        if ($maintenanceRequestItem) {
            return response()->json([
                'status' => 'ok',
                'maintenanceRequest' => $maintenanceRequestItem->maintenanceRequest,
                'maintenanceRequestItem' => $maintenanceRequestItem,
                'estimatedCost' => $maintenanceRequestItem->maintenanceRequest->estimated_cost,
                'message' => 'Maintenance request item is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Maintenance request item can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified maintenance request item from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($drId, $id)
    {
        $maintenanceRequestItem = $this->maintenanceRequestItems->find($id);
        $flag = $this->maintenanceRequestItems->destroy($id);
        if ($flag) {
            $maintenanceRequest = $this->maintenanceRequests->find($drId);
            return response()->json([
                'type' => 'success',
                'maintenanceRequest' => $maintenanceRequest,
                'message' => 'Maintenance request item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'maintenanceRequest' => $maintenanceRequestItem->maintenanceRequest,
            'message' => 'Maintenance request item can not deleted.',
        ], 422);
    }
}
