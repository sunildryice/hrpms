<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DataTables;
use Modules\Master\Repositories\LocalLevelRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Requests\LocalLevel\UpdateRequest;

class LocalLevelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  LocalLevelRepository $localLevels
     * @param  DistrictRepository $districts
     * @return void
     */
    public function __construct(
        LocalLevelRepository $localLevels,
        DistrictRepository $districts
    )
    {
        $this->localLevels = $localLevels;
        $this->districts = $districts;
    }

    /**
     * Display a listing of the localLevel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->localLevels->select([
                'id', 'district_id','local_level_name', 'created_by', 'updated_at'
            ])->with(['district.province']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-locallevel-modal-form" href="';
                    $btn .= route('master.local.levels.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    return $btn;
                })->addColumn('province', function ($row) {
                    return $row->getProvinceName();
                })->addColumn('district', function ($row) {
                    return $row->getDistrictName();
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('Master::LocalLevel.index');
    }

    /**
     * Show the form for editing the specified localLevel.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $localLevel = $this->localLevels->find($id);
        return view('Master::LocalLevel.edit')
            ->withLocalLevel($localLevel)
            ->withDistricts($this->districts->get());
    }

    /**
     * Update the specified localLevel in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $localLevel = $this->localLevels->update($id, $inputs);
        if ($localLevel) {
            return response()->json(['status' => 'ok',
                'localLevel' => $localLevel,
                'message' => 'LocalLevel is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'LocalLevel can not be updated.'], 422);
    }
}
