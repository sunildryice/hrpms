<?php

namespace Modules\VehicleRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\VehicleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Yajra\DataTables\DataTables;

class ClosedController extends Controller
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
            $data = $this->vehicleRequests->getClosed();

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
                $btn .= route('closed.vehicle.requests.show', $row->id) . '" rel="tooltip" title="View Vehicle Request">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" target="_blank" href="';
                $btn .= route('approved.vehicle.requests.print', $row->id) . '" rel="tooltip" title="Print Vehicle Request"><i class="bi bi-printer"></i></a>';
                return $btn;
            })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('VehicleRequest::Closed.index');
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
        return view('VehicleRequest::Closed.show')
            ->withVehicleRequest($vehicleRequest);
    }

}
