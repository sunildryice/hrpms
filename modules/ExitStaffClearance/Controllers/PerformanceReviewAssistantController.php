<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;
use Modules\ExitStaffClearance\Models\PerformanceReviewType;
use Modules\ExitStaffClearance\Notifications\PerformanceReviewCreated;
use Modules\ExitStaffClearance\Notifications\PerformanceReviewSubmitted;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\ExitStaffClearance\Requests\PerformanceReview\StoreRequest;
use Modules\ExitStaffClearance\Requests\PerformanceReview\UpdateRequest;
use Yajra\DataTables\DataTables;

class PerformanceReviewAssistantController extends Controller
{

    public function __construct(
        EmployeeRepository $employee,
        StaffClearanceRepository $staffClearance,
        PerformanceReviewType $staffClearanceType,
        StaffClearanceDepartment $clearanceDepartments,
        FiscalYearRepository $fiscalYear
    )
    {
        $this->employee                     = $employee;
        $this->staffClearance            = $staffClearance;
        $this->staffClearanceType        = $staffClearanceType;
        $this->clearanceDepartments    = $clearanceDepartments;
        $this->fiscalYear                   = $fiscalYear;
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $employeeIds = [];
            if($authUser->employee_id) {
                $employeeIds = $this->employee->select(['id', 'supervisor_id', 'cross_supervisor_id', 'next_line_manager_id'])
                    ->where('supervisor_id', $authUser->employee_id)
                    ->orWhere('cross_supervisor_id', $authUser->employee_id)
                    ->orWhere('next_line_manager_id', $authUser->employee_id)
                    ->pluck('id')->toArray();
            }

            $data = $this->staffClearance->with(['employee', 'fiscalYear', 'status', 'reviewType'])
                ->whereIn('employee_id', $employeeIds)
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($staffClearance) {
                    return $staffClearance->getEmployeeName();
                })
                ->addColumn('fiscal_year', function ($staffClearance) {
                    return $staffClearance->getFiscalYear();
                })
                ->addColumn('review_type', function ($staffClearance) {
                    return $staffClearance->getReviewType();
                })
                ->addColumn('review_from', function ($staffClearance) {
                    return $staffClearance->getReviewFromDate();
                })
                ->addColumn('review_to', function ($staffClearance) {
                    return $staffClearance->getReviewToDate();
                })
                ->addColumn('deadline_date', function ($staffClearance) {
                    return $staffClearance->getDeadlineDate();
                })
                ->addColumn('status', function ($staffClearance) {
                    return '<span class="' . $staffClearance->getStatusClass() . '">' . $staffClearance->getStatus() . '</span>';
                })
                ->addColumn('action', function ($staffClearance) use($authUser) {
                    $btn = '';
                    if ($authUser->can('view', $staffClearance)) {
                        $btn .= '<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.show', [$staffClearance->id]).'" rel="tooltip" title="View"><i class="bi bi-eye"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('ExitStaffClearance::Assistant.index');
    }
}
