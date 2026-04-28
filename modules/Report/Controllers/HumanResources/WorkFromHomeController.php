<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\StatusRepository;
use Modules\Report\Exports\HumanResources\WorkFromHomeExport;
use Modules\WorkFromHome\Enums\WorkFromHomeTypes;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;

class WorkFromHomeController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected WorkFromHomeRepository $workFromHomes,
        protected OfficeRepository $offices,
        protected StatusRepository $statuses
    ) {
    }

    public function index(Request $request)
    {
        $months = Helper::getMonthArray();
        $fiscalYear = $request->fiscal_year ? $this->fiscalYears->find($request->fiscal_year) : $this->fiscalYears->getCurrentFiscalYear();
        $employees = $this->employees->getAllEmployees();

        $wfhStatusIds = [
            config('constant.APPROVED_STATUS'),
            config('constant.SUBMITTED_STATUS'),
            config('constant.REJECTED_STATUS'),
        ];

        $query = $this->workFromHomes->select(['*'])
            // ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('status_id', $wfhStatusIds)
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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        $workFromHomes = $query->orderBy('start_date', 'desc')->paginate(100);

        $offices = $this->offices->getOffices();
        $typeOptions = WorkFromHomeTypes::options();
        $statuses = $this->statuses->whereIn('id', $wfhStatusIds)->get();

        return view('Report::HumanResources.WorkFromHome.index', [
            'employees' => $this->employees->getActiveEmployees(),
            'fiscalYears' => $this->fiscalYears->getFiscalYears(),
            'request_date' => $request->request_date,
            'requestData' => $request->all(),
            'offices' => $offices,
            'workFromHomes' => $workFromHomes,
            'months' => $months,
            'typeOptions' => $typeOptions,
            'statuses' => $statuses,
        ]);
    }

    public function export(Request $request)
    {
        $fiscalYear = $request->fiscal_year ? (int) $request->fiscal_year : null;
        $month = $request->month ? (int) $request->month : null;
        $office = $request->office ? (int) $request->office : null;
        $requestDate = $request->request_date ?: null;
        $employee = $request->filled('employee') ? $request->employee : null;
        $type = $request->filled('type') ? $request->type : null;
        $status     = $request->filled('status') ? (int) $request->status : null;

        return new WorkFromHomeExport($fiscalYear, $month, $office, $employee, $requestDate, $type, $status);
    }
}