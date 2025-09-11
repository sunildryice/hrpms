<?php

namespace Modules\Report\Controllers\HumanResources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\FamilyDetail;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Models\Office;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Report\Exports\HumanResources\EmployeeInsuranceExport;
use Yajra\DataTables\DataTables;

class EmployeeInsuranceController extends Controller
{
    private $employees;
    private $offices;
    public function __construct(
        EmployeeRepository $employees,
        OfficeRepository $offices
    )
    {
        $this->employees = $employees;
        $this->offices = $offices;
    }

    public function index(Request $request)
    {
        $data = $this->employees->query();
        $data->whereNotNull('activated_at')->orderBy('employee_code', 'asc');

        if ($request->filled('office_id'))
        {
            $data->where('office_id', '=', $request->office_id);
        }

        $employees = $data->with('tenures')->get();

        $offices = $this->offices->getOffices();

        return view('Report::HumanResources.EmployeeInsurance.index', compact('employees', 'offices'));
    }

    public function export(Request $request)
    {
        $office_id = $request->filled('office_id') ? $request->office_id : null;

        return new EmployeeInsuranceExport($office_id);
    }
}
