<?php

namespace Modules\Lta\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\Lta\Repositories\LtaContractItemRepository;
use Modules\Lta\Repositories\LtaContractRepository;
use Modules\Lta\Requests\Item\StoreRequest;
use Modules\Lta\Requests\Item\UpdateRequest;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ItemRepository;

class LtaItemController extends Controller
{

    protected $ltaContracts;
    protected $ltaItems;
    protected $destinationPath;
    protected $activityCodes;
    protected $donorCodes;
    protected $items;

    public function __construct(
        LtaContractRepository $ltaContracts,
        LtaContractItemRepository $ltaItems,
        ActivityCodeRepository $activityCodes,
        DonorCodeRepository $donorCodes,
        ItemRepository $items

    ) {
        $this->ltaContracts = $ltaContracts;
        $this->ltaItems = $ltaItems;
        $this->destinationPath = 'lta';
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->items = $items;
    }

    public function index(Request $request, $ltaId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $lta = $this->ltaContracts->find($ltaId);

            $data = $this->ltaItems->select(['*'])
                ->with(['item', 'unit'])->where('lta_contract_id', $ltaId)->get();

            $datatable = DataTables::of($data)
                ->addIndexColumn();
            // if ($authUser->can('update', $lta)) {
            $datatable->addColumn('action', function ($row) use ($authUser, $lta) {
                $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-item-modal-form" href="';
                $btn .= route('lta.items.edit', [$row->lta_contract_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                $btn .= 'data-href="' . route('lta.items.destroy', [$row->lta_contract_id, $row->id]) . '">';
                $btn .= '<i class="bi-trash"></i></a>';
                return $btn;
            });
            // }
            return $datatable->addColumn('item', function ($row) {
                return $row->getItemName();
            })->addColumn('unit', function ($row) {
                return $row->getUnitName();
            })->addColumn('specification', function ($row) {
                return $row->specification;
            })->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    public function create($ltaId)
    {
        $authUser = auth()->user();
        $lta = $this->ltaContracts->find($ltaId);
        // $this->authorize('update',$lta);

        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        return view('Lta::Item.create')
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withLta($lta)
            ->withItems($this->items->getActiveItems());
    }

    /**
     * Store the newly created lta item in storage.
     *
     * @param StoreRequest $request
     * @param $ltaId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $ltaId)
    {
        $lta = $this->ltaContracts->find($ltaId);
        $inputs = $request->validated();
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $inputs['lta_contract_id'] = $lta->id;
        $ltaItem = $this->ltaItems->create($inputs);
        if ($ltaItem) {
            return response()->json(['status' => 'ok',
                'ltaItem' => $ltaItem,
                'message' => 'LTA item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'LTA item can not be updated.'], 422);
    }

    public function edit($ltaId, $id)
    {
        $lta = $this->ltaContracts->find($ltaId);
        $ltaItem = $this->ltaItems->find($id);
        // $this->authorize('update',$lta);

        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $accountCodes = $ltaItem->activityCode ? $ltaItem->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();

        return view('Lta::Item.edit')
            ->withLtaItem($ltaItem)
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes);
    }

    public function update(UpdateRequest $request, $ltaId, $id)
    {
        $lta = $this->ltaContracts->find($ltaId);
        $ltaItem = $this->ltaItems->find($id);
        // $this->authorize('update',$lta);
        $inputs = $request->validated();
        $inputs['vat_flag'] = isset($request->vat_applicable);
        $ltaItem = $this->ltaItems->update($id, $inputs);
        if ($ltaItem) {
            return response()->json(['status' => 'ok',
                'ltaItem' => $ltaItem,
                'message' => 'LTA item is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'LTA item can not be updated.'], 422);
    }

    public function destroy($ltaId, $id)
    {
        $ltaItem = $this->ltaItems->find($id);
        $flag = $this->ltaItems->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'LTA item is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'LTA item can not deleted.',
        ], 422);
    }

}
