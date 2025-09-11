<?php

namespace Modules\Master\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\VehicleRepository;
use Modules\Master\Repositories\VehicleTypeRepository;
use Modules\Master\Requests\Vehicle\StoreRequest;
use Modules\Master\Requests\Vehicle\UpdateRequest;

use DataTables;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param OfficeRepository $offices
     * @param VehicleRepository $vehicles
     * @return void
     */
    public function __construct(
        OfficeRepository $offices,
        VehicleRepository $vehicles,
        VehicleTypeRepository $vehicleTypes
    )
    {
        $this->offices = $offices;
        $this->vehicles = $vehicles;
        $this->vehicleTypes = $vehicleTypes;
    }

    /**
     * Display a listing of the vehicle.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->vehicles->select(['*']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('office', function($row){
                    return $row->getOfficeName();
                })->addColumn('vehicleType', function($row){
                        return $row->vehicleType->getVehicleType();
                })->addColumn('created_by', function ($row) {
                    return $row->getCreatedBy();
                })->addColumn('updated_at', function ($row) {
                    return $row->getUpdatedAt();
                })->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-vehicle-modal-form" href="';
                    $btn .= route('master.vehicles.edit', $row->id) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('master.vehicles.destroy', $row->id) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('Master::Vehicle.index');
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $offices = $this->offices->getActiveOffices();
        $vehicleTypes = $this->vehicleTypes->get();
        return view('Master::Vehicle.create')
            ->withOffices($offices)
            ->withVehicleTypes($vehicleTypes);
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param \Modules\Master\Requests\Vehicle\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request)
    {
        $inputs = $request->validated();
        $inputs['created_by'] = auth()->id();
        $inputs['activated_at'] = date('Y-m-d H:i:s');
        $vehicle = $this->vehicles->create($inputs);
        if ($vehicle) {
            return response()->json(['status' => 'ok',
                'vehicle' => $vehicle,
                'message' => 'Vehicle is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Vehicle can not be added.'], 422);
    }

    /**
     * Display the specified vehicle.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vehicle = $this->vehicles->find($id);
        return response()->json(['status' => 'ok', 'vehicle' => $vehicle], 200);
    }

    /**
     * Show the form for editing the specified vehicle.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        $vehicle = $this->vehicles->find($id);
        $vehicleTypes = $this->vehicleTypes->get();
        $offices = $this->offices->getActiveOffices();
        return view('Master::Vehicle.edit')
            ->withOffices($offices)
            ->withVehicleTypes($vehicleTypes)
            ->withVehicle($vehicle);
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param \Modules\Master\Requests\Vehicle\UpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $id)
    {
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $vehicle = $this->vehicles->update($id, $inputs);
        if ($vehicle) {
            return response()->json(['status' => 'ok',
                'vehicle' => $vehicle,
                'message' => 'Vehicle is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Vehicle can not be updated.'], 422);
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        $flag = $this->vehicles->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Vehicle is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Vehicle can not deleted.',
        ], 422);
    }
}
