<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Office;
use Modules\Report\Exports\HumanResources\ConsultantProfileExport;
use Yajra\DataTables\DataTables;

class ConsultantProfileController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Employee::query();
            $data->where(function ($q) {
                $q->whereNull('employee_type_id')
                    ->orWhere('employee_type_id', '<>', config('constant.FULL_TIME_EMPLOYEE'));
            });
            $data->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('consultant_name', function ($row) {
                    return $row->getFullName();
                })
                ->addColumn('consultant_company', function ($row) {
                    return '';
                })
                ->addColumn('id_number', function ($row) {
                    return $row->employee_code;
                })
                ->addColumn('joined_date', function ($row) {
                    return $row->latestTenure->getJoinedDate();
                })
                ->addColumn('consultant_type', function ($row) {
                    return $row->employeeType?->title;
                })
                ->addColumn('position_latest', function ($row) {
                    return $row->latestTenure->getDesignationName();
                })
                ->addColumn('duty_station_latest', function ($row) {
                    return $row->getDutyStation();
                })
                ->addColumn('supervisor_name_latest', function ($row) {
                    return $row->getSupervisorName();
                })
                ->addColumn('current_address', function ($row) {
                    return $row->address->getPermanentAddress();
                })
                ->addColumn('mobile', function ($row) {
                    return $row->mobile_number;
                })
                ->addColumn('office_email', function ($row) {
                    return $row->official_email_address;
                })
                ->addColumn('citizenship_number', function ($row) {
                    return $row->citizenship_number;
                })
                ->addColumn('pan_vat_number', function ($row) {
                    return $row->pan_number;
                })
                ->addColumn('dob', function ($row) {
                    return $row->date_of_birth;
                })
                ->addColumn('gender', function ($row) {
                    return '';
                })
                ->addColumn('bank_details', function ($row) {
                    return '';
                })
                ->addColumn('leave_applicable', function ($row) {
                    return '';
                })
                ->addColumn('contract_end_date', function ($row) {
                    return '';
                })
                ->addColumn('contract_amendment_tenure', function ($row) {
                    return '';
                })
                ->addColumn('contract_ending_notice_period', function ($row) {
                    return '';
                })
                ->make(true);
        }

        $offices = Office::select(['id', 'office_name'])->get();

        return view('Report::HumanResources.ConsultantProfile.index', compact('offices'));
    }

    public function export(Request $request)
    {
        $start_date = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date) / 1000) : null;
        $end_date = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date) / 1000) : null;
        $office = $request->office ? $request->office : null;

        return new ConsultantProfileExport($start_date, $end_date, $office);
    }
}
