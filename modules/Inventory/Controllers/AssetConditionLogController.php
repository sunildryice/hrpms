<?php

namespace Modules\Inventory\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Inventory\Repositories\AssetConditionLogRepository;
use Modules\Inventory\Repositories\AssetRepository;
use Yajra\DataTables\DataTables;
use Modules\Inventory\Requests\AssetConditionLog\StoreRequest;
use Modules\Inventory\Requests\AssetConditionLog\UpdateRequest;
use Modules\Master\Repositories\ConditionRepository;

class AssetConditionLogController extends Controller
{
    private $assets;
    private $assetConditionLogs;
    private $conditions;

    /**
     * Create a new controller instance.
     *
     * @param AssetRepository $assets
     */
    public function __construct(
        AssetRepository $assets,
        AssetConditionLogRepository $assetConditionLogs,
        ConditionRepository $conditions
    )
    {
        $this->assets = $assets;
        $this->assetConditionLogs = $assetConditionLogs;
        $this->conditions = $conditions;
    }

    /**
     * Display a listing of the asset condition log
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $asset)
    {
        $authUser = auth()->user();
        $this->authorize('manage-inventory');

        if ($request->ajax()) {
            $asset = $this->assets->find($asset);
            $data = $asset->assetConditionLogs()->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('asset_condition', function ($row) {
                    return $row->getCondition();
                })->addColumn('description', function ($row) {
                    return $row->getDescription();
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->updated_at->format('Y-m-d');
                })->addColumn('action', function ($row) use ($asset) {
                    $btn = '';

                    if($row->id == $asset->latestConditionLog->id){
                        $btn .= '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-asset-condition-log-modal-form" href="';
                        $btn .= route('asset.condition.logs.edit', $row->id) . '" rel="tooltip" title="Edit"><i class="bi-pencil-square"></i></a>';
                    }

                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('asset.condition.logs.destroy', $row->id) . '" rel="tooltip" title="Delete">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('Inventory::Asset.index');
    }

    public function create($asset)
    {
        $asset = $this->assets->find($asset);
        $conditions = $this->conditions->getConditions();

        return view('Inventory::Asset.AssetConditionLog.create')
        ->withAsset($asset)
        ->withConditions($conditions);
    }


    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();

        $inputs['created_by'] = auth()->user()->id;
        $assetConditionLog = $this->assetConditionLogs->create($inputs);

        if ($assetConditionLog) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Asset condition log added successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Asset condition log could not be added.'
            ], 422);
        }
    }


    public function edit($assetConditionLog)
    {
        $assetConditionLog = $this->assetConditionLogs->find($assetConditionLog);
        $conditions = $this->conditions->getConditions();

        return view('Inventory::Asset.AssetConditionLog.edit')
            ->withAssetConditionLog($assetConditionLog)
            ->withConditions($conditions);
    }

    public function update(UpdateRequest $request, $assetConditionLog)
    {
        $inputs = $request->validated();

        $inputs['updated_by'] = auth()->user()->id;
        $assetConditionLog = $this->assetConditionLogs->update($assetConditionLog, $inputs);

        if ($assetConditionLog) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Asset condition log updated successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Asset condition log could not be updated.'
            ], 422);
        }
    }

    public function show($assetConditionLog)
    {
        $assetConditionLog = $this->assetConditionLogs->find($assetConditionLog);
        return $assetConditionLog;
    }

    public function destroy($assetConditionLog)
    {
        $assetConditionLog = $this->assetConditionLogs->destroy($assetConditionLog);
        if ($assetConditionLog) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Asset condition log deleted successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Asset condition log could not be deleted.'
            ], 422);
        }
    }

}
