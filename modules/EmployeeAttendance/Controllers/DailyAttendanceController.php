<?php

namespace Modules\EmployeeAttendance\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Models\AttendanceDetail;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;

class DailyAttendanceController extends Controller
{
    public function __construct(
        protected OfficeRepository $officeRepo,
        protected EmployeeRepository $employeeRepo,
        protected AttendanceDetailRepository $attendanceDetailRepo
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $selectedDate = $request->filled('selected_date')
                ? date('Y-m-d', (int) ($request->selected_date / 1000))
                : now()->format('Y-m-d');

            $isToday = Carbon::parse($selectedDate)->isToday();

            $query = $this->employeeRepo->getActiveEmployeesQuery();

            if ($request->filled('office_id')) {
                $query->whereHas('latestTenure', function ($q) use ($request) {
                    $q->where('office_id', $request->office_id);
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('staff_id', fn($emp) => $emp->requestId ?? '-')
                ->addColumn('employee_name', fn($emp) => $emp->getFullName())
                ->addColumn('time_in', function ($emp) use ($selectedDate) {
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    return $detail?->getCheckinTime() ?: '-';
                })
                ->addColumn('time_out', function ($emp) use ($selectedDate) {
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    return $detail?->getCheckoutTime() ?: '-';
                })
                ->addColumn('hours_worked', function ($emp) use ($selectedDate) {
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    return $detail && $detail->worked_hours
                        ? number_format($detail->worked_hours, 2)
                        : '-';
                })
                ->addColumn('remarks', function ($emp) use ($selectedDate) {
                    $date = Carbon::parse($selectedDate);
                    if ($date->isFuture()) {
                        return 'Future Date';
                    }

                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);

                    if ($detail && ($detail->checkin || $detail->checkout)) {
                        return 'Present';
                    }
                    if ($date->isWeekend()) {
                        return 'Weekend';
                    }

                    return 'Absent';
                })
                ->addColumn('action', function ($emp) use ($selectedDate, $isToday) {
                    // Hide action column completely for today
                    if ($isToday) {
                        return '<span class="text-muted">-</span>';
                    }

                    if (!auth()->user()->can('edit_daily_attendance')) {
                        return '<span class="text-muted">-</span>';
                    }

                    $date = Carbon::parse($selectedDate);

                    // Only allow edit for past dates (not future, already excluded above)
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    return '<button type="button" class="btn btn-sm btn-outline-primary edit-attendance-btn"
                            title="Edit Attendance"
                            data-employee-id="' . $emp->id . '"
                            data-date="' . $selectedDate . '"
                            data-checkin="' . ($detail?->checkin?->format('H:i') ?? '') . '"
                            data-checkout="' . ($detail?->checkout?->format('H:i') ?? '') . '">
                            <i class="bi bi-pencil-square"></i> 
                        </button>';
                })
                ->rawColumns(['action', 'remarks'])
                ->make(true);
        }

        $data = [
            'offices' => $this->officeRepo->getActiveOffices(),
            'can_edit_attendance' => auth()->user()->can('edit_daily_attendance'),
        ];

        return view('EmployeeAttendance::DailyAttendance.index', $data);
    }

    public function updateTime(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'checkin' => 'nullable|date_format:H:i',
            'checkout' => 'nullable|date_format:H:i|after_or_equal:checkin',
        ]);

        $employeeId = $request->employee_id;
        $date = $request->date;
        $checkin = $request->checkin ? $date . ' ' . $request->checkin . ':00' : null;
        $checkout = $request->checkout ? $date . ' ' . $request->checkout . ':00' : null;

        $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($employeeId, $date);

        if ($detail) {
            // Update existing record
            $detail->update([
                'checkin' => $checkin,
                'checkout' => $checkout,
            ]);
        } else {
            // Create new record ( create attendance master first)
            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $employeeId, 'month' => date('n', strtotime($date)), 'year' => date('Y', strtotime($date))],
                ['created_by' => auth()->id()]
            );

            $detail = AttendanceDetail::create([
                'attendance_master_id' => $attendance->id,
                'attendance_date' => $date,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'created_by' => auth()->id(),
            ]);
        }

        // recalculate worked_hours
        if ($checkin && $checkout) {
            $start = Carbon::parse($checkin);
            $end = Carbon::parse($checkout);
            $hours = $start->diff($end)->format('%H.%I');
            $detail->update(['worked_hours' => $hours]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully'
        ]);
    }
}