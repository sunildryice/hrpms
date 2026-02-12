<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Helper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;
use Modules\Report\Exports\HumanResources\OffDayWorkExport;

class OffDayWorkReportController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected OffDayWorkRepository $offDayWorks,
        protected OfficeRepository $offices
    ) {
    }

    public function index(Request $request)
    {
        $months = Helper::getMonthArray();
        $fiscalYear = $request->fiscal_year
            ? $this->fiscalYears->find($request->fiscal_year)
            : $this->fiscalYears->getCurrentFiscalYear();

        $query = $this->offDayWorks->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereYear('date', $fiscalYear->start_date);   // ← key field is 'date'

        if ($request->month) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->office) {
            $query->whereOfficeId($request->office);
        }
        if ($request->request_date) {
            $query->whereDate('request_date', $request->request_date);
        }
        // if ($request->off_day_date) {               
        //     $query->whereDate('date', $request->off_day_date);
        // }
        if ($request->employee) {
            $query->where('requester_id', $request->employee);
        }

        $offDayWorks = $query->orderBy('date', 'desc')->paginate(100);

        return view('Report::HumanResources.OffDayWork.index', [
            'employees' => $this->employees->getActiveEmployees(),
            'fiscalYears' => $this->fiscalYears->getFiscalYears(),
            'offices' => $this->offices->getOffices(),
            'months' => $months,
            'offDayWorks' => $offDayWorks,
            'requestData' => $request->all(),
            'request_date' => $request->request_date,
            'off_day_date' => $request->off_day_date ?? '',
        ]);
    }

    public function export(Request $request)
    {
        $fiscalYear = $request->fiscal_year ? (int) $request->fiscal_year : null;
        $month = $request->month ? (int) $request->month : null;
        $office = $request->office ? (int) $request->office : null;
        $requestDate = $request->request_date ?: null;
        $offDayDate = $request->off_day_date ?: null;
        $employee = $request->filled('employee') ? $request->employee : null;

        return Excel::download(
            new OffDayWorkExport($fiscalYear, $month, $office, $employee, $requestDate, $offDayDate),
            'off_day_work_report.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}