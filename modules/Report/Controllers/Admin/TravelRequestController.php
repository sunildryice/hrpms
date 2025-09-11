<?php

namespace Modules\Report\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Report\Exports\Admin\TravelRequestExport;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\TravelRequest\Models\TravelRequestItinerary;
use Yajra\DataTables\DataTables;

class TravelRequestController extends Controller
{
    public function __construct(
        DistrictRepository $districts,
        EmployeeRepository $employees
    )
    {
        $this->districts = $districts;
        $this->employees = $employees;
    }
    public function index(Request $request)
    {
        $data = TravelRequest::query()->with(['travelClaim.approvedLog', 'requester.employee']);
        $data->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);

        // $data = TravelRequestItinerary::query();

        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d', ((int)$request->end_date)/1000);
                if($start_date <= $end_date) {
                    $data->whereDate('departure_date', '>=', $start_date)
                          ->whereDate('return_date', '<=', $end_date);
                    // $data->whereHas('travelRequest', function($q) use($start_date, $end_date) {
                    //     $q->whereDate('departure_date', '>=', $start_date)
                    //       ->whereDate('return_date', '<=', $end_date);
                    // });
                }
            }

            if ($request->has('employee') && $request->employee) {
                $employee_user_id = $request->employee;
                $data->where('requester_id', $employee_user_id);
            }

            if ($request->has('duty_station') && $request->duty_station) {
                $dutyStationId = $request->duty_station;
                $data->whereHas('requester', function($q) use($dutyStationId) {
                    $q->whereHas('employee', function($q) use($dutyStationId) {
                        $q->whereHas('latestTenure', function($q) use($dutyStationId) {
                            $q->where('duty_station_id', $dutyStationId);
                        });
                    });
                });
            }

            if ($request->has('purpose_of_travel') && $request->purpose_of_travel) {
                $purposeOfTravel = $request->purpose_of_travel;
                $data->where('purpose_of_travel', 'LIKE', '%'.$purposeOfTravel.'%');
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('travel_number', function ($row){
                return $row->getTravelRequestNumber();
            })
            ->addColumn('employee_name', function ($row){
                return $row->getRequesterName();
            })
            ->addColumn('designation', function ($row){
                return $row->requester->employee->latestTenure->getDesignationName();
            })
            ->addColumn('duty_station', function ($row){
                return $row->requester->employee->latestTenure->getDutyStation();
            })
            ->addColumn('date_from', function ($row){
                return $row->getDepartureDate();
            })
            ->addColumn('date_to', function ($row){
                return $row->getReturnDate();
            })
            ->addColumn('total_days', function ($row){
                return $row->getTotalDays();
            })
            ->addColumn('mode_of_travel', function ($row){
                return $row->getTravelMode();
            })
            ->addColumn('travel_location', function ($row){
                return $row->final_destination;
            })
            ->addColumn('purpose_of_travel', function ($row){
                return $row->purpose_of_travel;
            })
            ->addColumn('approved', function ($row){
                return $row->getIsApproved();
            })
            ->addColumn('amended', function ($row){
                return $row->getIsAmended();
            })
            ->addColumn('travel_claim_submitted_date', function ($row){
                return $row->travelClaim?->getSubmittedDate();
                return $row->travelClaim?->created_at->format('M d, Y');
            })
            ->addColumn('travel_claim_approved_date', function ($row){
                return $row->travelClaim ?->getApprovedDate();
            })
            ->addColumn('travel_claim_reimbursed_date', function ($row){
                return '';
            })
            ->make(true);
        }

        $array = [
            'dutyStations'  => $this->districts->getDistricts(),
            'employees'     => $this->employees->getActiveEmployees()
        ];

        return view('Report::Admin.TravelRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $employee = $request->employee ? $request->employee : null;
        $duty_station = $request->duty_station ? $request->duty_station : null;
        $purpose_of_travel = $request->purpose_of_travel ? $request->purpose_of_travel : null;

        return new TravelRequestExport($start_date, $end_date, $employee, $duty_station, $purpose_of_travel);
    }
}
