<?php

namespace Modules\VehicleRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;

class InvolvedController extends Controller
{
    public function __construct(
        protected VehicleRequestRepository $vehicleRequests
    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->vehicleRequests->with(['vehicleRequestType', 'requester', 'status', 'logs'])
                ->whereHas('logs', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                })->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_number', function ($row) {
                    return $row->getVehicleRequestNumber();
                })->addColumn('start_datetime', function ($row) {
                    return $row->getStartDatetime();
                })->addColumn('end_datetime', function ($row) {
                    return $row->getEndDatetime();
                })->addColumn('vehicle_request_type', function ($row) {
                    return $row->getVehicleRequestType();
                })->addColumn('requester', function ($row) {
                    return $row->getRequesterName();
                })->addColumn('status', function ($row) {
                    return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
                })->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('vehicle.requests.show', $row->id) . '" title="View Vehicle Request"><i class="bi bi-eye"></i></a>';
                    return $btn;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('VehicleRequest::Involved.index');
    }
}
