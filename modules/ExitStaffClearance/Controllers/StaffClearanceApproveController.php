<?php

namespace Modules\ExitStaffClearance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ExitStaffClearance\Models\StaffClearanceLog;
use Modules\ExitStaffClearance\Notifications\StaffClearanceApproved;
use Modules\ExitStaffClearance\Notifications\StaffClearanceEndorsed;
use Modules\ExitStaffClearance\Notifications\StaffClearanceReturned;
use Modules\ExitStaffClearance\Repositories\StaffClearanceDepartmentRepository;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\ExitStaffClearance\Requests\Approve\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class StaffClearanceApproveController extends Controller
{
    public function __construct(
        protected StaffClearanceRepository $staffClearance,
        protected StaffClearanceLog $staffClearanceLog,
        protected StaffClearanceDepartmentRepository $departments,
        protected UserRepository $users
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->staffClearance->select('*')
                // ->with(['employee', 'fiscalYear', 'status', 'reviewType'])
                ->where('status_id', config('constant.VERIFIED3_STATUS'))
                ->where('approver_id', $authUser->id)
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
                    if ($authUser->can('approve', $row)) {
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('staff.clearance.approve.create', $row->id).'"  data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="Fill Staff Clearance"><i class="bi bi-ui-checks"></i></a>';
                    }

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('ExitStaffClearance::Approve.index');
    }

    public function create(Request $request, $id)
    {

        $staffClearance = $this->staffClearance
            ->with(['employee', 'handoverNote', 'exitInterview', 'exitAssetHandover'])
            ->find($id);
        $approvers = $this->users->permissionBasedUsers('approve-staff-clearance');
        $this->authorize('approve', $staffClearance);

        return view('ExitStaffClearance::Approve.create', [
            'staffClearance' => $staffClearance,
            'approvers' => $approvers,
            'records' => $staffClearance->records,
            'departments' => $this->departments->getParentDepartments(),
            'authUser' => auth()->user(),
        ]);
    }

    public function store(StoreRequest $request, $clearanceId)
    {
        $staffClearance = $this->staffClearance->find($clearanceId);
        $this->authorize('approve', $staffClearance);
        $inputs = $request->validated();

        $staffClearance = $this->staffClearance->approve($clearanceId, $inputs);

        if ($staffClearance) {
            $message = 'Staff Clearance successfully approved.';
            if($staffClearance->status_id == config('constant.VERIFIED_STATUS')){
                $message = 'Staff Clearance successfully returned.';
                $staffClearance->certifier->notify(new StaffClearanceReturned($staffClearance));
            }else{
                $staffClearance->certifier->notify(new StaffClearanceApproved($staffClearance));
                $staffClearance->employee->user->notify(new StaffClearanceApproved($staffClearance));
            }

            return redirect()->route('staff.clearance.approve.index')->withSuccessMessage($message);
        }

        return redirect()->back()->withErrorMessage('Staff Clearance Cannot be updated');
    }
}
