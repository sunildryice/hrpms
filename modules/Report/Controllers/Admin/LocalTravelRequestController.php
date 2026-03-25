<?php

namespace Modules\Report\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Report\Exports\Admin\LocalTravelRequestExport;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Yajra\DataTables\DataTables;

class LocalTravelRequestController extends Controller
{
    public function __construct(
        EmployeeRepository    $employees,
        LocalTravelRepository $localTravels,
    )
    {
        $this->employees = $employees;
        $this->localTravels = $localTravels;
    }

    public function index(Request $request)
    {
        $data = $this->localTravels->with(['requester.employee'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);

        if ($request->ajax()) {

            if ($request->has('employee') && $request->employee) {
                $employee_user_id = $request->employee;
                $data->where('requester_id', $employee_user_id);
            }

            if ($request->has('title') && $request->title) {
                $purposeOfTravel = $request->title;
                $data->where('title', 'LIKE', '%' . $purposeOfTravel . '%');
            }
            $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('local_travel_number', function ($row) {
                    return $row->getLocalTravelNumber();
                })
                ->addColumn('employee_name', function ($row) {
                    return $row->getRequesterName();
                })
                ->addColumn('designation', function ($row) {
                    return $row->requester->employee->latestTenure->getDesignationName();
                })
                ->addColumn('duty_station', function ($row) {
                    return $row->requester->employee->latestTenure->getDutyStation();
                })
                ->addColumn('purpose_of_travel', function ($row) {
                    return $row->purpose_of_travel;
                })
                ->addColumn('approved', function ($row) {
                    return $row->getIsApproved();
                })
                ->make(true);
        }

        $array = [
            'employees' => $this->employees->getActiveEmployees()
        ];

        return view('Report::Admin.LocalTravelRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date) / 1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date) / 1000) : null;
        $employee = $request->employee ? $request->employee : null;
        $duty_station = $request->duty_station ? $request->duty_station : null;
        $purpose_of_travel = $request->purpose_of_travel ? $request->purpose_of_travel : null;

        return new TravelRequestExport($start_date, $end_date, $employee, $duty_station, $purpose_of_travel);
    }
}
