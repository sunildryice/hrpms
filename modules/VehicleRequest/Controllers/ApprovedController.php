<?php

namespace Modules\VehicleRequest\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\VehicleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\VehicleRequest\Requests\Approve\AssignRequest;
use Modules\VehicleRequest\Notifications\VehicleRequestClosed;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;

class ApprovedController extends Controller
{
    private $fiscalYears;
    private $vehicleRequests;
    private $users;
    private $vehicles;
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
            $data = $this->vehicleRequests->getApproved();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('office', function ($row) {
                return $row->getOfficeName();
            })->addColumn('request_number', function ($row) {
                return $row->getVehicleRequestNumber();
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
                $btn .= route('approved.vehicle.requests.show', $row->id) . '" rel="tooltip" title="View Vehicle Request">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                if ($authUser->can('assignVehicle', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approved.vehicle.requests.assign.create', $row->id) . '" rel="tooltip" title="Assign Vehicle">';
                    $btn .= '<i class="bi bi-droplet-fill"></i></a>';
                }
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                $btn .= route('approved.vehicle.requests.print', $row->id) . '" rel="tooltip" title="Print Vehicle Request"><i class="bi bi-printer"></i></a>';

                if($authUser->can('close', $row)){
                    $btn .= '&emsp;<a class="btn btn-danger btn-sm close-vehicle-modal-form" href="';
                    $btn .= route('close.vehicle.requests.create', $row->id) . '" rel="tooltip" title="Close"><i class="bi bi-x-circle"></i></a>';
                }

                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('VehicleRequest::Approved.index');
    }

    public function create($vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('assignVehicle', $vehicleRequest);

        $query = $this->vehicles->select(['id', 'vehicle_number', 'passenger_capacity'])
            ->whereNotNull('activated_at');
        if ($vehicleRequest->assigned_departure_datetime) {
            $query->whereNotIn('id', function ($vrf) use ($vehicleRequest) {
                $vrf->select('assigned_vehicle_id')
                    ->from('vehicle_requests')
                    ->whereBetween('departure_date', [$vehicleRequest->assigned_departure_datetime, $vehicleRequest->assigned_arrival_date])
                    ->orWhereBetween('arrival_date', [$vehicleRequest->assigned_departure_datetime, $vehicleRequest->assigned_arrival_date])
                    ->groupby('assigned_vehicle_id');
            })->orWhereIn('id', [$vehicleRequest->assigned_vehicle_id]);
        }
        $vehicles = $query->orderBy('vehicle_number', 'asc')->get();

        return view('VehicleRequest::Approved.assign')
            ->withAuthUser($authUser)
            ->withVehicleRequest($vehicleRequest)
            ->withVehicles($vehicles);
    }

    public function store(AssignRequest $request, $vehicleRequestId)
    {
        $authUser = auth()->user();
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);
        $this->authorize('assignVehicle', $vehicleRequest);
        $inputs = $request->validated();
        $inputs['status_id'] = $vehicleRequest->status_id;
        $inputs['updated_by'] = $authUser->id;
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        if ($request->btn == 'submit') {
            $inputs['assigned_departure_datetime'] = $vehicleRequest->start_datetime->format('Y-m-d H:i');
            $inputs['assigned_arrival_datetime'] = $vehicleRequest->start_datetime->endOfDay()->format('Y-m-d H:i');
            $inputs['status_id'] = config('constant.ASSIGNED_STATUS');
        }

        $vehicleRequest = $this->vehicleRequests->assign($vehicleRequest->id, $inputs);
        if ($vehicleRequest) {
            $message = 'Vehicle request is successfully updated.';
            if ($vehicleRequest->status_id == config('constant.ASSIGNED_STATUS')) {
                $message = 'Vehicle is successfully assigned to vehicle request.';
//                $vehicleRequest->requester->notify(new VehicleAssigned($vehicleRequest));
            }

            return redirect()->route('approved.vehicle.requests.index')
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
        return view('VehicleRequest::Approved.show')
            ->withVehicleRequest($vehicleRequest);
    }

    /**
     * Print the specified vehicle request.
     *
     * @param $vehicleRequestId
     * @return mixed
     */
    public function print($vehicleRequestId)
    {
        $vehicleRequest = $this->vehicleRequests->find($vehicleRequestId);

        return view('VehicleRequest::Approved.print')
            ->withVehicleRequest($vehicleRequest);
    }

    /**
     * Close a vehicle request by procurement officers
     * @param mixed $id
     * @return void
     */
    public function close($id)
    {
        $vehicleRequest = $this->vehicleRequests->find($id);
        $this->authorize('close', $vehicleRequest);
        $vehicleRequest = $this->vehicleRequests->close($id);
        if ($vehicleRequest) {
            $vehicleRequest->requester->notify(new VehicleRequestClosed($vehicleRequest));
            return response()->json([
                'type' => 'success',
                'message' => 'Vehicle request closed successfully'
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Vehicle Request Cannot be Closed',
        ], 422);
    }
}
