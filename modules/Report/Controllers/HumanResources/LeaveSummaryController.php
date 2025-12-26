<?php

namespace Modules\Report\Controllers\HumanResources;

use App\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;
use Modules\Report\Exports\HumanResources\LeaveSummaryExport;

class LeaveSummaryController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employees,
        protected FiscalYearRepository $fiscalYears,
        protected LeaveTypeRepository $leaveTypes,
        protected LeaveRepository $leaves,
    ) {}

    public function index(Request $request)
    {
        $months = Helper::getMonthArray();
        $fiscalYear = $request->fiscal_year ? $this->fiscalYears->find($request->fiscal_year) : $this->fiscalYears->getCurrentFiscalYear();

        $query = $this->leaves->select(['*'])
            ->whereYear('reported_date', $fiscalYear->start_date);
        if ($request->month) {
            $query->whereMonth('reported_date', $request->month);
        }
        $leaves = $query->get();

        $data = Employee::query();
        $data->whereNotNull('activated_at')
        ->where(function($query) {
            $query->whereNull('employee_type_id')
            ->orWhere('employee_type_id', '=', config('constant.FULL_TIME_EMPLOYEE'));
        });

        if ($request->filled('employee')) {
            $employeeCode = $request->employee;
            $data->where('employee_code', $employeeCode);
        }
        $data->whereIn('id', $leaves->pluck('employee_id')->toArray());
        $filteredEmployees = $data->get();

        $leaveTypes = $this->leaveTypes->select(['*'])
            ->whereIn('id', $leaves->pluck('leave_type_id')->toArray())->get();

        $array = [
            'employees' => $this->employees->getActiveEmployees(),
            'fiscalYears' => $this->fiscalYears->getFiscalYears(),
            'filteredEmployees' => $filteredEmployees,
            'employee' => $request->filled('employee') ? $request->employee : '',
            'fiscal_year' => $request->filled('fiscal_year') ? $this->fiscalYears->getFiscalYearById($request->fiscal_year) : $this->fiscalYears->getCurrentFiscalYearTitle(),
            'leaveTypes' => $leaveTypes,
            'leaves' => $leaves,
            'months' => $months,
        ];

        return view('Report::HumanResources.LeaveSummary.index', $array);
    }

    public function export(Request $request)
    {
        $employeeCode = $request->employee ? (int) $request->employee : null;
        $fiscalYear = $request->fiscal_year ? (int) $request->fiscal_year : null;
        $month = $request->month ? (int) $request->month : null;

        return new LeaveSummaryExport($employeeCode, $fiscalYear, $month);
    }
}
