<?php

namespace Modules\Master\Controllers;

use DataTables;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProvinceRepository;
use Modules\Master\Repositories\LocalLevelRepository;

use Modules\Master\Requests\HealthFacility\StoreRequest;
use Modules\Master\Repositories\HealthFacilityRepository;
use Modules\Master\Requests\HealthFacility\UpdateRequest;

class HealthFacilityController extends Controller
{
    private $healthFacilities;

    public function __construct(
        HealthFacilityRepository $healthFacilities,
        ProvinceRepository $provinces,
        DistrictRepository $districts,
        LocalLevelRepository $localLevels
    )
    {
        $this->healthFacilities = $healthFacilities;
        $this->provinces = $provinces;
        $this->districts = $districts;
        $this->localLevels = $localLevels;
    }

    /**
     * Display a listing of the health facility
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->healthFacilities->with(['province','district', 'localLevel'])
            ->select(['*'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-health-facility-modal-form" href="';
                    $btn .= route('master.health.facilities.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.health.facilities.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('title', function ($row) {
                    return $row->getTitle();
                })
                ->addColumn('province', function ($row) {
                    return $row->getProvince();
                })
                ->addColumn('district', function ($row) {
                    return $row->getDistrict();
                })
                ->addColumn('local_level', function ($row) {
                    return $row->getLocalLevel();
                })
                ->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::HealthFacility.index');
    }

    /**
     * Show the form for creating a new health facility.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $provinces = $this->provinces->select(['id', 'province_name'])->get();
        return view('Master::HealthFacility.create')->withProvinces($provinces);
    }

    /**
     * Store a newly created health facility in storage.
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $healthFacility = $this->healthFacilities->create($inputs);
        if ($healthFacility) {
            return response()->json([
                'healthFacility' => $healthFacility,
                'message' => 'Health facility is successfully added.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Health facility can not be added.'
        ], 422);
    }

    /**
     * Display the specified Health facility
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $healthFacility = $this->healthFacilities->find($id);
        return response()->json(['status' => 'ok', 'healthFacility' => $healthFacility], 200);
    }

    /**
     * Show the form for editing the specified Health facility
     *
     * @param int $id
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $healthFacility = $this->healthFacilities->find($id);
        $provinces = $this->provinces->select(['id', 'province_name'])->get();
        $districts = $healthFacility->province->districts;
        $localLevels = $healthFacility->district->localLevels;
        return view('Master::HealthFacility.edit',compact('healthFacility', 'provinces', 'districts', 'localLevels'));
    }

    /**
     * Update the specified Health facility in storage.
     *
     * @param \Modules\Master\Requests\HealthFacility\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $healthFacility = $this->healthFacilities->update($id, $inputs);
        if ($healthFacility) {
            return response()->json(['status' => 'ok',
                'healthFacility' => $healthFacility,
                'message' => 'Health facility is successfully updated.'], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Health facility can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified Health facility from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->healthFacilities->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Health facility is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Health facility can not deleted.',
        ], 422);
    }
}
