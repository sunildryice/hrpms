<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\EmployeeRequest\Models\EmployeeRequest;
use Modules\Master\Models\District;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Report\Exports\HumanResources\EmployeeRequisitionExport;
use Yajra\DataTables\DataTables;

class EmployeeRequisitionController extends Controller
{
    public function __construct(
        protected DistrictRepository      $districts,
        protected FiscalYearRepository    $fiscalYears
    )
    {
    }

    public function index(Request $request)
    {
        $data = EmployeeRequest::query();
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

            if ($request->has('position') && $request->position) {
                if ($request->position !== 'all') {
                    $data->where('position_title', $request->position);
                }
            }

            if ($request->has('duty_station') && $request->duty_station) {
                if ($request->duty_station !== 'all') {
                    $data->where('duty_station_id', $request->duty_station);
                }
            }

            if ($request->has('fiscal_year') && $request->fiscal_year) {
                $fiscalYearId = $request->fiscal_year;
                $data->where('fiscal_year_id', $fiscalYearId);
            }

            $data->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('ref_number', function ($row){
                return $row->id;
            })
            ->addColumn('position_title', function ($row){
                return $row->position_title;
            })
            ->addColumn('duty_station', function ($row){
                return $row->getDutyStation();
            })
            ->addColumn('requested_level', function ($row){
                return $row->position_level;
            })
            ->addColumn('requested_date', function ($row){
                return $row->requested_date ?? $row->created_at->format('Y-m-d');
            })
            ->addColumn('type_of_employement', function ($row){
                return $row->employeeType?->title;
            })
            ->addColumn('for_fiscal_year', function ($row){
                return $row->getFiscalYear();
            })
            ->addColumn('replacement_for', function ($row){
                return $row->replacement_for;
            })
            ->addColumn('date_required_from', function ($row){
                return $row->required_date;
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
            ->addColumn('requested_by', function ($row){
                return $row->getRequesterName();
            })
            ->addColumn('requested_date', function ($row){
                return $row->requested_date ?? $row->created_at->format('Y-m-d');
            })
            ->addColumn('approved', function ($row){
                return $row->approver_id ? 'Yes' : 'No';
            })
            ->addColumn('approved_date', function ($row){
                return $row->getApprovedDate();
            })
            ->addColumn('vacancy_type', function ($row){
                return '';
            })
            ->addColumn('vacancy_portfolio', function ($row){
                return '';
            })
            ->addColumn('vacancy_date', function ($row){
                return $row->requested_date ?? $row->created_at->format('Y-m-d');
            })
            ->addColumn('vacancy_deadline', function ($row){
                return $row->required_date;
            })
            ->addColumn('recruitment_process', function ($row){
                return '';
            })
            ->addColumn('recruited', function ($row){
                return '';
            })
            ->addColumn('joined_date', function ($row){
                return '';
            })
            ->addColumn('remarks', function ($row){
                return '';
            })
            ->make(true);
        }

        $array = [
            'positions'     => array_unique(EmployeeRequest::pluck('position_title')->toArray()),
            'dutyStations'  => $this->districts->getDistricts(),
            'fiscalYears'   => $this->fiscalYears->getFiscalYears()
        ];

        return view('Report::HumanResources.EmployeeRequisition.index', $array);
    }

    public function export(Request $request)
    {
        $start_date     = $request->start_date ? date('Y-m-d 00:00:00', ((int)$request->start_date)/1000) : null;
        $end_date       = $request->end_date ? date('Y-m-d 00:00:00', ((int)$request->end_date)/1000) : null;
        $position       = $request->position ? $request->position : null;
        $duty_station   = $request->duty_station ? $request->duty_station : null;
        $fiscal_year     = $request->fiscal_year ? $request->fiscal_year : null;

        return new EmployeeRequisitionExport($start_date, $end_date, $position, $duty_station, $fiscal_year);
    }
}
