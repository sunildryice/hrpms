<?php

namespace Modules\Report\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Report\Exports\Admin\VehicleMovementExport;
use Modules\VehicleRequest\Models\VehicleRequest;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Yajra\DataTables\DataTables;

class VehicleMovementController extends Controller
{
    public function __construct(
        VehicleRequestRepository $vehicleRequests
    )
    {
        $this->vehicleRequests = $vehicleRequests;
    }

    public function index(Request $request)
    {        
        $data = $this->vehicleRequests->approvedAndAssignedVehicleRequests();
        // $data = $this->vehicleRequests->approvedVehicleRequests();
        
        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                         ->whereDate('created_at', '<', $end_date);
                }
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('vehicle_request_number', function ($row){
                return $row->getVehicleRequestNumber();
            })
            ->addColumn('office', function ($row){
                return $row->getOfficeName();
            })
            ->addColumn('vehicle_request_type', function ($row) {
                return $row->getVehicleRequestType();
            })
            ->addColumn('hired_date_from', function ($row){
                return $row->getStartDatetime();
            })
            ->addColumn('hired_date_to', function ($row){
                return $row->getEndDateTime();
            })
            ->addColumn('travel_from', function ($row){
                return $row->travel_from;
            })
            ->addColumn('travel_to', function ($row){
                return $row->destination;
            })
            ->addColumn('user', function ($row){
                return $row->getRequesterName();
            })
            ->addColumn('purpose_of_travel', function ($row){
                return $row->purpose_of_travel;
            })
            ->addColumn('vehicle_type', function ($row){
                return $row->getVehicleTypes();
            })
            ->addColumn('tentative_cost', function ($row){
                return $row->tentative_cost;
            })
            ->addColumn('pickup_point', function ($row){
                return $row->pickup_place;
            })
            ->addColumn('pickup_time', function ($row){
                return $row->pickup_time;
            })
            ->addColumn('end_time', function ($row){
                return $row->getEndDatetime();
            })
            ->addColumn('request_approved_date', function ($row){
                return $row->getRequestApprovalDate();
            })
            ->addColumn('vehicle_contractor', function ($row){
                return '';
            })
            ->addColumn('bill_number', function ($row){
                return '';
            })
            ->addColumn('bill_date', function ($row){
                return '';
            })
            ->addColumn('amount', function ($row){
                return '';
            })
            ->addColumn('vat', function ($row){
                return '';
            })
            ->addColumn('total_amount', function ($row){
                return '';
            })
            ->addColumn('tds', function ($row){
                return '';
            })
            ->addColumn('net_payment', function ($row){
                return '';
            })
            ->addColumn('payment_approved_date', function ($row){
                return '';
            })
            ->make(true);
        }

        return view('Report::Admin.VehicleMovement.index');
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;

        return new VehicleMovementExport($start_date, $end_date);
    }
}
