<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\HumanResources\WorkFromHomeExport;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class WorkFromHomeController extends Controller
{
    public function __construct(
        protected EmployeeRepository     $employees,
        protected FiscalYearRepository   $fiscalYears,
        protected WorkFromHomeRepository $workFromHomes,
        protected OfficeRepository       $offices
    ) {}

    public function index(Request $request)
    {
        $months = Helper::getMonthArray();
        $fiscalYear = $request->fiscal_year ? $this->fiscalYears->find($request->fiscal_year) : $this->fiscalYears->getCurrentFiscalYear();
        $employees = $this->employees->getAllEmployees();

        $query = $this->workFromHomes->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereYear('request_date', $fiscalYear->start_date);

        if ($request->month) {
            $query->whereMonth('request_date', $request->month);
        }
        if ($request->office) {
            $query->whereOfficeId($request->office);
        }
        if ($request->request_date) {
            $query->where('request_date', $request->request_date);
        }
        if ($request->employee) {
            $query->where('requester_id', '=', $request->employee);
        }

        $workFromHomes = $query->orderBy('start_date', 'desc')->paginate(100);

        $offices = $this->offices->getOffices();

        return view('Report::HumanResources.WorkFromHome.index', [
            'employees'      => $this->employees->getActiveEmployees(),
            'fiscalYears'    => $this->fiscalYears->getFiscalYears(),
            'request_date'   => $request->request_date,
            'requestData'    => $request->all(),
            'offices'        => $offices,
            'workFromHomes'  => $workFromHomes,
            'months'         => $months,
        ]);
    }

    public function export(Request $request)
    {
        $fiscalYear   = $request->fiscal_year ? (int)$request->fiscal_year : null;
        $month        = $request->month ? (int)$request->month : null;
        $office       = $request->office ? (int)$request->office : null;
        $requestDate  = $request->request_date ?: null;
        $employee     = $request->filled('employee') ? $request->employee : null;

        return new WorkFromHomeExport($fiscalYear, $month, $office, $employee, $requestDate);
    }
}