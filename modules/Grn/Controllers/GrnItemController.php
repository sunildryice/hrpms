<?php

namespace Modules\Grn\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Grn\Repositories\GrnItemRepository;
use Modules\Grn\Repositories\GrnRepository;

use Modules\Grn\Requests\Item\FromOrderUpdateRequest;
use Modules\Grn\Requests\Item\StoreRequest;
use Modules\Grn\Requests\Item\UpdateRequest;

use DataTables;
use Modules\Grn\Requests\Item\FromRequestUpdateRequest;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;
use ReflectionClass;

class GrnItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param GrnRepository $grns
     * @param GrnItemRepository $grnItems
     * @param ItemRepository $items
     */
    public function __construct(
        protected ActivityCodeRepository $activityCodes,
        protected DonorCodeRepository    $donorCodes,
        protected GrnRepository          $grns,
        protected GrnItemRepository      $grnItems,
        protected ItemRepository         $items
    )
    {
    }

    /**
     * Display a listing of the grns
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $grnId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $grn = $this->grns->find($grnId);
            // $data = $this->grnItems->select(['*'])
            //     ->with(['item', 'unit', 'grnitemable' => function ($q) {
            //         $q->select(['id', 'specification']);
            //     }])->whereGrnId($grnId);
            $data = $this->grnItems->select(['*'])
                ->with(['item', 'unit', 'grnitemable'])->whereGrnId($grnId);

            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $grn)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $grn) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                    $btn .= route('grns.items.edit', [$row->grn_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('grns.items.destroy', [$row->grn_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->addColumn('item', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })->addColumn('specification', function ($row) {
                return $row->grnitemable ? $row->grnitemable->specification : $row->specification;
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating new grn item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($grnId)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);

        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        return view('Grn::Item.create')
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withGrn($grn)
            ->withItems($this->items->getActiveItems());
    }

    /**
     * Store the newly created grn item in storage.
     *
     * @param StoreRequest $request
     * @param $grnId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $grnId)
    {
        $grn = $this->grns->find($grnId);
        $this->authorize('update', $grn);
        $inputs = $request->validated();
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['grn_id'] = $grn->id;
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $inputs['discount_amount'] = $request->discount_amount ?: 0;
        $grnItem = $this->grnItems->create($inputs);
        if ($grnItem) {
            return response()->json(['status' => 'ok',
                'grnItem' => $grnItem,
                'message' => 'GRN item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'GRN item can not be updated.'], 422);
    }

    /**
     * Show the form for editing the specified grn item.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($grnId, $id)
    {
        $authUser = auth()->user();
        $grn = $this->grns->find($grnId);
        $grnItem = $this->grnItems->find($id);
        $this->authorize('update', $grn);

        $view = view('Grn::Item.edit');
        $grnableType = $grn->grnable_type;
        if ($grnableType) {
            $reflection = new ReflectionClass($grnableType);
            $modelName = $reflection->getShortName();
            if ($modelName == "PurchaseRequest") {
                $view = view('Grn::Item.editRequest');
            } else {
                $view = view('Grn::Item.editOrder');
            }
        }

        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $accountCodes = $grnItem->activityCode ? $grnItem->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();

        return $view->withGrnItem($grnItem)
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes);
    }

    /**
     * Update the specified grn in storage.
     *
     * @param UpdateRequest $request
     * @param $grnId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $grnId, $id)
    {

        $grn = $this->grns->find($grnId);
        $grnItem = $this->grnItems->find($id);
        $this->authorize('update', $grn);
        $inputs = $request->validated();
        $inputs['total_price'] = $request->quantity * $request->unit_price;
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['discount_amount'] = $request->discount_amount ?: 0;
        if($grn->grnable?->id){
            $inputs['activity_code_id'] = $grnItem->grnitemable->activity_code_id;
            $inputs['account_code_id'] = $grnItem->grnitemable->account_code_id;
            $inputs['donor_code_id'] = $grnItem->grnitemable->donor_code_id;
        }
        $grnItem = $this->grnItems->update($id, $inputs);
        if ($grnItem) {
            return response()->json(['status' => 'ok',
                'grnItem' => $grnItem,
                'message' => 'GRN item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'GRN item can not be updated.'], 422);
    }

    /**
     * Update the specified grn in storage.
     *
     * @param UpdateRequest $request
     * @param $grnId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function fromOrderUpdate(FromOrderUpdateRequest $request, $grnId, $id)
    {
        $grnItem = $this->grnItems->find($id);
        $this->authorize('update', $grnItem->grn);
        $inputs = $request->validated();
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['total_price'] = $request->quantity * ($inputs['unit_price'] ?? $grnItem->unit_price);
        $grnItem = $this->grnItems->update($id, $inputs);
        if ($grnItem) {
            return response()->json(['status' => 'ok',
                'grnItem' => $grnItem,
                'message' => 'GRN item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'GRN item can not be updated.'], 422);
    }


    /**
     * Update the specified grn in storage.
     *
     * @param UpdateRequest $request
     * @param $grnId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function fromRequestUpdate(FromRequestUpdateRequest $request, $grnId, $id)
    {
        $grnItem = $this->grnItems->find($id);
        $this->authorize('update', $grnItem->grn);
        $inputs = $request->validated();
        $inputs['total_price'] = $request->quantity * $grnItem->unit_price;
        $grnItem = $this->grnItems->update($id, $inputs);
        if ($grnItem) {
            return response()->json(['status' => 'ok',
                'grnItem' => $grnItem,
                'message' => 'GRN item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'GRN item can not be updated.'], 422);
    }



    /**
     * Remove the specified grn from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($grnId, $id)
    {
        $grnItem = $this->grnItems->find($id);
        $this->authorize('delete', $grnItem->grn);
        $flag = $this->grnItems->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'GRN item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'GRN item can not deleted.',
        ], 422);
    }
}
