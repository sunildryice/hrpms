<?php

namespace Modules\EmployeeAttendance\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Notifications\AttendanceApproved;
use Modules\EmployeeAttendance\Notifications\AttendanceReturned;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\EmployeeAttendance\Requests\AttendanceApprove\StoreRequest;
use Modules\Master\Repositories\DonorCodeRepository;
use Yajra\DataTables\DataTables;

class ApproveController extends Controller
{
    public function __construct(
        protected AttendanceRepository $attendance,
        protected AttendanceDetailRepository $attendanceDetail,
        protected DonorCodeRepository $donor,
        protected LeaveRepository $leaves,

    ) {}

    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {
            $data = Attendance::where(function ($q) use ($authUser) {
                $q->where('approver_id', $authUser->id);
                $q->where('status_id', config('constant.VERIFIED_STATUS'));
            })->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('employee_name', function ($row) {
                    return $row->employee->getFullName();
                })
                ->addColumn('year', function ($row) {
                    return $row->year;
                })
                ->addColumn('month', function ($row) {
                    return date('F', mktime(0, 0, 0, $row->month, 10));
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('attendance.approve.create', $row->id).'" rel="tooltip" title="View Attendance Detail"><i class="bi bi-box-arrow-in-up-right"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);

        }

        return view('EmployeeAttendance::AttendanceApprove.index');
    }

    public function create($attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);
        $summary = $attendance->getTotalSummary();

        $donors = $this->donor->getActiveDonorCodes();
        $this->authorize('approve', $attendance);

        $array = [
            'auth_user' => auth()->user(),
            'attendance' => $attendance,
            'attendanceId' => $attendanceId,
            'dates' => $this->attendanceDetail->getAttendanceDetail($attendanceId),
            'donors' => $donors,
            'unrestrictedDonor' => $this->donor->getUnrestrictedDonor(),
            'enabledDonors' => $donors,
            // 'leaves'                        => $this->leaves->with(['leaveType'])->where('employee_id', $attendance->employee->id)->get(),
            'leaves' => $this->leaves->getMonthlyEmployeeLeaves($attendance->employee->id, $attendance->year, $attendance->month),

            'donor_charges' => $attendance->getDonorCharges(),
            'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
            'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
            'total_worked_hours' => $summary->get('total_worked_hours'),
            'total_charged_hours' => $summary->get('total_charged_hours'),
            'total_charged_percentage' => $summary->get('total_charged_percentage'),
        ];

        return view('EmployeeAttendance::AttendanceApprove.create', $array);
    }

    public function store(StoreRequest $request, $attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);

        $this->authorize('approve', $attendance);

        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $attendance = $this->attendance->approve($attendanceId, $inputs);

        if ($attendance) {
            $message = '';
            if ($attendance->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Attendance is successfully returned.';
                $attendance->requester->notify(new AttendanceReturned($attendance));
            } else {
                $message = 'Attendance is successfully approved.';
                $attendance->requester->notify(new AttendanceApproved($attendance));
            }

            return redirect()->route('attendance.approve.index')->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Attendance cannot be approved.');
    }

    public function view($id) {}
}
