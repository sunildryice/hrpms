<?php

namespace Modules\VehicleRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Master\Repositories\VehicleRepository;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use DataTables;
use Modules\VehicleRequest\Notifications\VehicleRequestAssigned;
use Modules\VehicleRequest\Notifications\VehicleRequestRejected;
use Modules\VehicleRequest\Notifications\VehicleRequestReturned;
use Modules\VehicleRequest\Notifications\VehicleRequestAssignedDriver;
use Modules\VehicleRequest\Requests\Approve\AssignRequest;

class AssignController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param FiscalYearRepository $fiscalYears
     * @param VehicleRequestRepository $vehicleRequests
     * @param UserRepository $users
     * @param VehicleRepository $vehicles
     */
    public function __construct(
        FiscalYearRepository $fiscalYears,
        VehicleRequestRepository $vehicleRequests,
        UserRepository $users,
        VehicleRepository $vehicles
    ) {
        $this->fiscalYears = $fiscalYears;
        $this->vehicleRequests = $vehicleRequests;
        $this->users = $users;
        $this->vehicles = $vehicles;
    }

    /**
     * Display a listing of the vehicle requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->vehicleRequests->with(['status', 'vehicleRequestType', 'requester'])->select(['*'])
                ->whereIn('status_id', [config('constant.SUBMITTED_STATUS')])
                ->where('vehicle_request_type_id', 1)
                ->where('approver_id', $authUser->id)
                ->orderBy('vehicle_request_number', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getVehicleRequestNumber();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('start_datetime', function ($row) {
                    return $row->getStartDatetime();
                })->addColumn('end_datetime', function ($row) {
                    return $row->getEndDatetime();
                })->addColumn('vehicle_request_type', function ($row) {
                    return $row->getVehicleRequestType();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('assign.vehicle.requests.show', $row->id) . '" rel="tooltip" title="View Vehicle Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    if ($authUser->can('assignVehicle', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('assign.vehicle.requests.create', $row->id) . '" rel="tooltip" title="Assign Vehicle">';
                        $btn .= '<i class="bi bi-droplet-fill"></i></a>';
                    }
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('VehicleRequest::Assign.index');
    }

    public function create($vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        if ($vehicleRequest->vehicle_request_type_id == 2) {
            return redirect()->route('approve.vehicle.requests.create', $vehicleRequest->id);
        }
        $this->authorize('assignVehicle', $vehicleRequest);

        $query = $this->vehicles->select(['id', 'vehicle_number', 'passenger_capacity'])
            ->whereNotNull('activated_at');
        if ($vehicleRequest->assigned_departure_datetime) {
            $query->whereNotIn('id', function ($vrf) use ($vehicleRequest) {
                $vrf->select('assigned_vehicle_id')
                    ->from('vehicle_requests')
                    ->whereBetween('start_datetime', [$vehicleRequest->assigned_departure_datetime, $vehicleRequest->assigned_arrival_date])
                    ->orWhereBetween('end_datetime', [$vehicleRequest->assigned_departure_datetime, $vehicleRequest->assigned_arrival_date])
                    ->groupby('assigned_vehicle_id');
            })->orWhereIn('id', [$vehicleRequest->assigned_vehicle_id]);
        }
        $vehicles = $query->orderBy('vehicle_number', 'asc')->get();
        $drivers = $this->users->permissionBasedUsers('vehicle-request-driver');

        // Prevent Driver assigned to two vehicles at same time
        // $drivers = $this->users->permissionBasedUsers('vehicle-request-driver')
        //     ->whereNotIn('id', function ($q) use ($vehicleRequest) {
        //         $q->select('driver_id')
        //             ->from('vehicle_requests')
        //             ->whereBetween('start_datetime', [$vehicleRequest->start_datetime, $vehicleRequest->end_datetime])
        //             ->orWhereBetween('end_datetime', [$vehicleRequest->start_datetime, $vehicleRequest->end_datetime]);
        //     });

        return view('VehicleRequest::Assign.create')
            ->withAuthUser($authUser)
            ->withVehicleRequest($vehicleRequest)
            ->withDrivers($drivers)
            ->withVehicles($vehicles);
    }

    public function store(AssignRequest $request, $vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('assignVehicle', $vehicleRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = $authUser->id;
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $vehicleRequest = $this->vehicleRequests->assign($vehicleRequest->id, $inputs);
        if ($vehicleRequest) {
            $message = 'Vehicle request is successfully updated.';
            if ($vehicleRequest->status_id == config('constant.ASSIGNED_STATUS')) {
                $message = 'Vehicle is successfully assigned to vehicle request.';
                $vehicleRequest->requester->notify(new VehicleRequestAssigned($vehicleRequest));
                if ($vehicleRequest->driver) {
                    $vehicleRequest->driver->notify(new VehicleRequestAssignedDriver($vehicleRequest));
                }
            }
            if ($vehicleRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Vehicle request is successfully returned.';
                $vehicleRequest->requester->notify(new VehicleRequestReturned($vehicleRequest));
            }
            if ($vehicleRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Vehicle request is successfully rejected.';
                $vehicleRequest->requester->notify(new VehicleRequestRejected($vehicleRequest));
            }


            return redirect()->route('assign.vehicle.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Vehicle can not be assigned to vehicle request.');
    }

    /**
     * Show the specified vehicle request.
     *
     * @param $vehicleRequestId
     * @return mixed
     */
    public function show($vehicleRequestId)
    {
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);

        return view('VehicleRequest::Assign.show')
            ->withVehicleRequest($vehicleRequest);
    }
}
