<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Report\Exports\HumanResources\EmployeeExitClearanceExport;
use Modules\Report\Exports\HumanResources\EmployeeExitInterviewExport;

class EmployeeExitClearanceController extends Controller
{
    public function __construct(
        EmployeeRepository $employees,
        DistrictRepository $districts,
        DesignationRepository $designations,
        ExitHandOverNoteRepository $exitHandoverNotes
    )
    {
        $this->employees = $employees;
        $this->districts = $districts;
        $this->designations = $designations;
        $this->exitHandoverNotes = $exitHandoverNotes;
    }
    public function index(Request $request)
    {
        $exitHandoverNotes = ExitHandOverNote::query();

        $employeeId = '';
        $dutyStationId = '';
        $designationId = '';
        $startDate = '';
        $endDate = '';

        if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            if($startDate < $endDate) {
                $exitHandoverNotes->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate);
            }
        }

        if ($request->has('employee') && $request->employee) {
            $employeeId = $request->employee;
            $exitHandoverNotes->where('employee_id', $employeeId);
        }

        if ($request->has('designation') && $request->designation) {
            $designationId = $request->designation;
            $exitHandoverNotes->whereHas('employee', function ($q) use($designationId) {
                $q->whereHas('latestTenure', function ($q) use($designationId) {
                    $q->where('designation_id', $designationId);
                });
            });
        }

        if ($request->has('duty_station') && $request->duty_station) {
            $dutyStationId = $request->duty_station;
            $exitHandoverNotes->whereHas('employee', function ($q) use($dutyStationId) {
                $q->whereHas('latestTenure', function ($q) use($dutyStationId) {
                    $q->where('duty_station_id', $dutyStationId);
                });
            });
        }

        $data = $exitHandoverNotes->get();

        $array = [
            'exitHandoverNotes'     => $data,
            'employees'             => $this->employees->getActiveEmployees(),
            'designations'          => $this->designations->getActiveDesignations(),
            'dutyStations'          => $this->districts->getDistricts(),
            'employeeId'            => $employeeId,
            'designationId'         => $designationId,
            'dutyStationId'         => $dutyStationId,
            'startDate'             => $startDate,
            'endDate'               => $endDate
        ];

        return view('Report::HumanResources.EmployeeExitClearance.index', $array);
    }

    public function export(Request $request)
    {
        $startDate      = $request->start_date ? $request->start_date : '';
        $endDate        = $request->end_date ? $request->end_date : '';
        $employee       = $request->employee ? $request->employee : '';
        $designation    = $request->designation ? $request->designation : '';
        $dutyStation    = $request->duty_station ? $request->duty_station : '';

        return new EmployeeExitClearanceExport($startDate, $endDate, $employee, $designation, $dutyStation);
    }
}