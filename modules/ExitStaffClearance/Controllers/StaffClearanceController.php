<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\ExitStaffClearance\Models\PerformanceReviewType;
use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\ExitStaffClearance\Repositories\StaffClearanceDepartmentRepository;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class StaffClearanceController extends Controller
{
    public function __construct(
        protected EmployeeRepository $employee,
        protected StaffClearanceRepository $staffClearance,
        protected PerformanceReviewType $staffClearanceType,
        protected StaffClearanceDepartmentRepository $departments,
        protected FiscalYearRepository $fiscalYear,
        protected UserRepository $users
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->staffClearance
                ->with(['employee.user'])
                ->orderBy('created_at', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->getEmployeeName();
                })->addColumn('last_duty_date', function ($row) {
                    return $row->getLastDutyDate();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('resignation_date', function ($row) {
                    return $row->getResignationDate();
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('staff.clearance.show', $row->id).'"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Show Employee Clearance"><i class="bi-eye"></i></a>';
                    if ($authUser->can('edit', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('staff.clearance.edit', $row->id).'"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Fill Staff Clearance"><i class="bi bi-ui-checks"></i></a>';
                    }
                    if ($authUser->can('delete', $row)) {
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="'.route('employee.exits.destroy', $row->id).'" rel="tooltip" title="Delete Employee Exit">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a href = "'.route('staff.clearance.print', $row->id).'" target="_blank" class="btn btn-outline-primary btn-sm" rel="tooltip" title="Clearance Print">';
                        $btn .= '<i class="bi bi-printer"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('ExitStaffClearance::index');
    }

    public function clearanceForm(Request $request, $clearanceId)
    {
        $clearance = $this->staffClearance->find($clearanceId);

        $departments = $this->departments->getParentDepartments();
        $records = $clearance->records;
        $authUser = auth()->user();

        $html = view('ExitStaffClearance::Partials.Table.clearance-form', compact('departments', 'records', 'clearance', 'authUser'))->render();

        return response()->json(['formTable' => $html]);
    }

    public function payableIndex(Request $request, $clearanceId)
    {
        $clearance = $this->staffClearance->with(['employeeExitPayable'])->find($clearanceId);

        $html = view('ExitStaffClearance::Partials.Table.payable', compact('clearance'))->render();

        return response()->json(['tableHtml' => $html]);
    }

    public function employeeIndex(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->staffClearance->where('requester_id', '=', auth()->user()->id)->orderBy('created_at', 'desc')->get();

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
                ->addColumn('status', function ($staffClearance) {
                    return '<span class="'.$staffClearance->getStatusClass().'">'.$staffClearance->getStatus().'</span>';
                })
                ->addColumn('action', function ($staffClearance) use ($authUser) {
                    $btn = '<a class="btn btn-sm btn-outline-primary" href="';
                    $btn .= route('performance.employee.show', [$staffClearance->id]).'" rel="tooltip" title="View Performance Review"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('employeeFill', $staffClearance)) {
                        $btn .= '&emsp;<a class="btn btn-sm btn-outline-primary" href="';
                        $btn .= route('performance.fill', [$staffClearance->id]).'" rel="tooltip" title="Fill Performance Review Form"><i class="bi bi-ui-checks"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('ExitStaffClearance::Employee.index');
    }

    public function show($id)
    {
        $staffClearance = $this->staffClearance->find($id);

        // $this->authorize('edit', $staffClearance);

        return view('ExitStaffClearance::show', [
            'staffClearance' => $staffClearance,
            'records' => $staffClearance->records,
            'departments' => $this->departments->getParentDepartments(),
            'authUser' => auth()->user(),
        ]);
    }

    public function print($id)
    {
        $staffClearance = $this->staffClearance->find($id);

        // $this->authorize('managePerformance', $staffClearance);
        return view('ExitStaffClearance::print', [
            'staffClearance' => $staffClearance,
            'records' => $staffClearance->records,
            'departments' => $this->departments->getParentDepartments(),
            'authUser' => auth()->user(),
        ]);

    }

    public function edit($staffClearance)
    {
        $staffClearance = $this->staffClearance
            ->with(['employee','employee.user.goodRequestAssets', 'handoverNote', 'exitInterview', 'exitAssetHandover'])
            ->find($staffClearance);
        $endorsers = $this->users->permissionBasedUsers('endorse-staff-clearance');
        // $this->authorize('edit', $staffClearance);

        return view('ExitStaffClearance::edit', [
            'staffClearance' => $staffClearance,
            'endorsers' => $endorsers,
            // 'records' => $staffClearance->records,
            // 'departments' => $this->departments->getParentDepartments(),
            'authUser' => auth()->user(),
        ]);
    }
}
