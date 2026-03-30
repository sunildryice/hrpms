<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Gender;
use Modules\Master\Models\Office;
use Modules\Report\Exports\HumanResources\EmployeeProfileExport;
use Yajra\DataTables\DataTables;

class EmployeeProfileController extends Controller
{
    public function index(Request $request)
    {
        // $employee = Employee::first();
        // $gender = $employee->gender->title;
        // dd($gender);

        $data = Employee::query()->with(['finance', 'employeeGender', 'address.permanent_province', 'latestTenure.dutyStation', 'medicalCondition.bloodGroup',
            'address.permanent_district', 'address.permanent_local_level', 'latestTenure.supervisor', 'maritalStatus', 'latestTenure.designation']);

        if ($request->ajax()) {
            if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
                $start_date = date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000);
                $end_date = date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000);
                if ($start_date < $end_date) {
                    $data->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<', $end_date);
                }
            }

            if ($request->has('office') && $request->office) {
                $office_id = $request->office;
                $data->where('office_id', $office_id);
            }

            if ($request->has('gender') && $request->gender) {
                $data->where('gender', $request->gender);
            }

            if ($request->has('active')) {
                if ($request->active === '1') {
                    $data->whereNotNull('activated_at');
                } elseif ($request->active === '0') {
                    $data->whereNull('activated_at');
                }
            }

            // filter consultants out
            $data->where(function ($q) {
                $q->whereNull('employee_type_id')
                    ->orWhere('employee_type_id', '=', config('constant.FULL_TIME_EMPLOYEE'));
            });

            $data = $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('staff_name', function ($row) {
                    return $row->getFullName();
                })
                ->addColumn('staff_id', function ($row) {
                    return $row->employee_code;
                })
                ->addColumn('joined_date', function ($row) {
                    return $row->latestTenure->getJoinedDate();
                })
                ->addColumn('position', function ($row) {
                    return $row->latestTenure->getDesignationName();
                })
                ->addColumn('duty_station', function ($row) {
                    return $row->latestTenure->duty_station;
                })
                ->addColumn('district', function ($row) {
                    return $row->getDutyStation();
                })
                ->addColumn('supervisor_name', function ($row) {
                    return $row->getSupervisorName();
                })
                ->addColumn('current_address', function ($row) {
                    return $row->address?->getPermanentAddress();
                })
                ->addColumn('mobile', function ($row) {
                    return $row->mobile_number;
                })
                ->addColumn('office_email', function ($row) {
                    return $row->official_email_address;
                })
                ->addColumn('citizenship_no', function ($row) {
                    return $row->citizenship_number;
                })
                ->addColumn('pan_no', function ($row) {
                    return $row->pan_number;
                })
                ->addColumn('ssf_no', function ($row) {
                    return $row->finance->ssf_number;
                })
                ->addColumn('cit_no', function ($row) {
                    return $row->finance->cit_number;
                })
                ->addColumn('dob', function ($row) {
                    return $row->date_of_birth;
                })
                ->addColumn('gender', function ($row) {
                    return $row->getGender();
                })
                ->addColumn('blood_group', function ($row) {
                    return $row->medicalCondition?->bloodGroup?->title ?: '';
                })
                ->addColumn('marital_status', function ($row) {
                    return $row->getMaritalStatus();
                })
                ->addColumn('bank_detail', function ($row) {
                    return $row->getBankDetail();
                })
                ->addColumn('probationary_complete', function ($row) {
                    return isset($row->probation_complete_date) ? 'Yes' : 'No';
                })
                ->addColumn('active_employee', function ($row) {
                    return $row->getActiveStatus();
                })
                ->addColumn('last_working_date', function ($row) {
                    return $row->last_working_date;
                })
                ->make(true);
        }

        $offices = Office::select(['id', 'office_name'])->get();
        $genders = Gender::get();

        return view('Report::HumanResources.EmployeeProfile.index', compact('offices', 'genders'));
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int) $request->start_date) / 1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int) $request->end_date) / 1000) : null;
        $office = $request->office ? $request->office : null;
        $gender = $request->gender ? $request->gender : null;
        $active = null;
        if ($request->has('active')) {
            $active = $request->active;
        }

        return new EmployeeProfileExport($start_date, $end_date, $office, $gender, $active);
    }
}
