<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Models\ExitFeedback;
use Modules\Master\Models\ExitQuestion;
use Modules\Master\Models\ExitRating;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ExitFeedbackRepository;
use Modules\Master\Repositories\ExitQuestionRepository;
use Modules\Master\Repositories\ExitRatingRepository;
use Modules\Report\Exports\HumanResources\EmployeeExitInterviewExport;

class EmployeeExitInterviewController extends Controller
{
    public function __construct(
        EmployeeRepository      $employees,
        DistrictRepository      $districts,
        DesignationRepository   $designations,
        ExitQuestionRepository  $exitQuestions,
        ExitFeedbackRepository  $exitFeedbacks,
        ExitRatingRepository    $exitRatings
    )
    {
        $this->employees        = $employees;
        $this->districts        = $districts;
        $this->designations     = $designations;
        $this->exitQuestions    = $exitQuestions;
        $this->exitFeedbacks    = $exitFeedbacks;
        $this->exitRatings      = $exitRatings;
    }

    public function index(Request $request)
    {
        $employeeCode       = '';
        $dutyStationId      = '';
        $designationId      = '';
        $lastWorkingDate    = '';

        $records = $this->employees->with('exitInterviews')
                                    ->whereHas('exitInterviews', function($q){
                                        $q->latest();
                                        $q->where('status_id', config('constant.APPROVED_STATUS'));
                                    })->newQuery();


        if ($request->filled('employee')) {
            $employeeCode = $request->employee;
            $records->where('employee_code', $employeeCode);
        }

        if ($request->filled('designation')) {
            $designationId = $request->designation;
            $records->whereHas('latestTenure', function ($q) use($designationId) {
                $q->where('designation_id', $designationId);
            });
        }

        if ($request->filled('duty_station')) {
            $dutyStationId = $request->duty_station;
            $records->whereHas('latestTenure', function ($q) use($dutyStationId) {
                $q->where('duty_station_id', $dutyStationId);
            });
        }

        if ($request->filled('last_working_date')) {
            $lastWorkingDate = $request->last_working_date;
            $records->where('last_working_date', '=', $lastWorkingDate);
        }

        $records = $records->get();

        $array = [
            'questions' => $this->exitQuestions->all(),
            'feedbacks' => $this->exitFeedbacks->all(),
            'ratings'   => $this->exitRatings->all(),
            'employees' => $this->employees->getActiveEmployees(),
            'designations'  => $this->designations->getActiveDesignations(),
            'dutyStations'  => $this->districts->getDistricts(),
            'employeeCode' => $employeeCode,
            'designationId' => $designationId,
            'dutyStationId' => $dutyStationId,
            'lastWorkingDate' => $lastWorkingDate,
            'records'           => $records
        ];

        return view('Report::HumanResources.EmployeeExitInterview.index', $array);
    }

    public function export(Request $request)
    {
        $employee           = $request->employee ? $request->employee : null;
        $designation        = $request->designation ? $request->designation : null;
        $dutyStation        = $request->duty_station ? $request->duty_station : null;
        $lastWorkingDate    = $request->last_working_date ? $request->last_working_date : null;

        return new EmployeeExitInterviewExport($employee, $designation, $dutyStation, $lastWorkingDate);
    }
}