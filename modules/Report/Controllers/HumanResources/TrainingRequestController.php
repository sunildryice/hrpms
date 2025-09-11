<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\DepartmentRepository;
use Modules\Master\Repositories\DesignationRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Report\Exports\HumanResources\TrainingRequestExport;
use Modules\TrainingRequest\Models\TrainingRequest;
use Yajra\DataTables\DataTables;

class TrainingRequestController extends Controller
{
    public function __construct(
        DepartmentRepository    $departments,
        DesignationRepository   $designations,
        DistrictRepository      $districts,
        EmployeeRepository      $employees
    )
    {
        $this->departments  = $departments;
        $this->designations = $designations;
        $this->districts    = $districts;
        $this->employees    = $employees;
    }
    public function index(Request $request)
    {        
        $data = TrainingRequest::query();
        $data->where('status_id', config('constant.APPROVED_STATUS'));

        
        if($request->ajax()) {
            if($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int)$request->start_date)/1000);
                $end_date = date('Y-m-d 00:00:00', ((int)$request->end_date)/1000);
                if($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                         ->whereDate('created_at', '<', $end_date);
                }
            }

            if ($request->has('employee') && $request->employee) {
                $employee_user_id = $request->employee;
                $data->where('created_by', $employee_user_id);
            }

            if ($request->has('designation') && $request->designation) {
                $designationId = $request->designation;
                $data->whereHas('requester', function($q) use($designationId) {
                    $q->whereHas('employee', function($q) use($designationId) {
                        $q->whereHas('latestTenure', function($q) use($designationId) {
                            $q->where('designation_id', $designationId);
                        });
                    });
                });
            }

            if ($request->has('department') && $request->department) {
                $departmentId = $request->department;
                $data->whereHas('requester', function($q) use($departmentId) {
                    $q->whereHas('employee', function($q) use($departmentId) {
                        $q->whereHas('latestTenure', function($q) use($departmentId) {
                            $q->where('department_id', $departmentId);
                        });
                    });
                });
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

            if ($request->has('training_name') && $request->training_name) {
                $trainingName = $request->training_name;
                $data->where('title', 'LIKE', '%'.$trainingName.'%');
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('ref_number', function ($row){
                return $row->getTrainingRequestNumber();
            })
            ->addColumn('employee_name', function ($row){
                return $row->requester->employee->getFullName();
            })
            ->addColumn('designation', function ($row){
                return $row->requester->employee->getDesignationName();
            })
            ->addColumn('department', function ($row){
                return $row->requester->employee->getDepartmentName();
            })
            ->addColumn('duty_station', function ($row){
                return $row->requester->employee->getDutyStation();
            })
            ->addColumn('name_of_course', function ($row){
                return $row->title;
            })
            ->addColumn('training_organizer', function ($row){
                return '';
            })
            ->addColumn('date_of_course_begin', function ($row){
                return $row->getStartDate();
            })
            ->addColumn('date_of_course_end', function ($row){
                return $row->getEndDate();
            })
            ->addColumn('time_of_course', function ($row){
                return $row->getTotalDays();
            })
            ->addColumn('project', function ($row){
                return '';
            })
            ->addColumn('account_code', function ($row){
                return $row->getAccountCode();
            })
            ->addColumn('activity_code', function ($row){
                return $row->getActivityCode();
            })
            ->addColumn('donor_code', function ($row){
                return '';
            })
            ->addColumn('training_cost', function ($row){
                return $row->course_fee;
            })
            ->addColumn('approved_date', function ($row){
                return $row->getTrainingRequestApprovedDate();
            })
            ->addColumn('training_report', function ($row){
                return $row->getTrainingReportSubmissionStatus();
            })
            ->addColumn('remarks', function ($row){
                return '';
            })
            ->make(true);
        }

        $array = [
            'departments'   => $this->departments->getActiveDepartments(),
            'designations'  => $this->designations->getActiveDesignations(),
            'dutyStations'  => $this->districts->getDistricts(),
            'employees'     => $this->employees->getActiveEmployees()
        ];

        return view('Report::HumanResources.TrainingRequest.index', $array);
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $employee = $request->employee ? $request->employee : null;
        $designation = $request->designation ? $request->designation : null;
        $department = $request->department ? $request->department : null;
        $dutyStation = $request->duty_station ? $request->duty_station : null;
        $trainingName = $request->training_name ? $request->training_name : null;

        return new TrainingRequestExport($start_date, $end_date, $employee, $designation, $department, $dutyStation, $trainingName);
    }
}
