<?php

namespace Modules\VehicleRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Yajra\DataTables\DataTables;

class DriverVehicleRequestController extends Controller
{
    public function __construct(
        protected VehicleRequestRepository $vehicleRequests
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = $this->vehicleRequests->getDriverAssigned();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', fn($row) => $row->getVehicleRequestNumber())
                ->addColumn('requester', fn($row) => $row->getRequesterName())
                ->addColumn('office', fn($row) => $row->getOfficeName())
                ->addColumn('start_datetime', fn($row) => $row->getStartDatetime())
                ->addColumn('end_datetime', fn($row) => $row->getEndDatetime())
                ->addColumn('vehicle', fn($row) => $row->getAssignedVehicleNumber())
                ->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $url = route('assigned.vehicle.requests.show', $row->id);
                    return '<a href="' . $url . '" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye"></i>
            </a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('VehicleRequest::Assigned.index');
    }

    public function show($id)
    {
        $vehicleRequest = $this->vehicleRequests->find($id);

        return view('VehicleRequest::Assigned.show', compact('vehicleRequest'));
    }
}
