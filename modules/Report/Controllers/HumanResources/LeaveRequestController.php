<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\HumanResources\LeaveRequestExport;

class LeaveRequestController extends Controller
{
    public function __construct(
        protected EmployeeRepository     $employees,
        protected FiscalYearRepository   $fiscalYears,
        protected LeaveTypeRepository    $leaveTypes,
        protected LeaveRequestRepository $leaveRequests,
        protected OfficeRepository       $offices
    )
    {
    }

    public function index(Request $request)
    {
        $months = Helper::getMonthArray();
        $fiscalYear = $request->fiscal_year ? $this->fiscalYears->find($request->fiscal_year) : $this->fiscalYears->getCurrentFiscalYear();
        $employees = $this->employees->getAllEmployees();

        $query = $this->leaveRequests->select(['*'])
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

        $leaveRequests = $query->orderBy('start_date', 'desc')->paginate(100);

        $offices = $this->offices->getOffices();
        $array = [
            'employees' => $this->employees->getActiveEmployees(),
            'fiscalYears' => $this->fiscalYears->getFiscalYears(),
            'request_date' => $request->request_date,
            'requestData' => $request->all(),
            'offices' => $offices,
            'leaveRequests' => $leaveRequests,
            'months' => $months,
        ];

        return view('Report::HumanResources.LeaveRequest.index', $array);
    }

    public function export(Request $request)
    {
        $fiscalYear = $request->fiscal_year ? (int)$request->fiscal_year : null;
        $month = $request->month ? (int)$request->month : null;
        $office = $request->office ? (int)$request->office : null;
        $requestDate = $request->request_date ?: null;
        $employee = $request->filled('employee') ? $request->employee : null;

        return new LeaveRequestExport($fiscalYear, $month, $office, $employee, $requestDate);
    }
}
