<?php

namespace Modules\EmployeeAttendance\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Models\AttendanceDetail;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\Master\Repositories\OfficeRepository;
use Yajra\DataTables\DataTables;

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

            $dateObj = Carbon::parse($selectedDate);
            $isToday = $dateObj->isToday();

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

                    if (!$detail || !$detail->checkin) {
                        return '-';
                    }
                    $checkinTime = Carbon::parse($detail->checkin)->format('H:i:s');
                    $checkin = Carbon::parse($selectedDate . ' ' . $checkinTime)->startOfMinute();

                    // $officeCheckin = Carbon::parse($selectedDate . ' ' . config('constant.OFFICE_CHECKIN_TIME'));
                    $officeCheckin = Carbon::parse($selectedDate . ' ' . $detail->getOfficeCheckin())->startOfMinute();

                    // if ($detail->weekend_type_id == config('constant.Saturday')) {
                    //     $officeCheckin = Carbon::parse($selectedDate . ' ' . config('constant.OFFICE_FIELD_CHECKIN_TIME'));
                    // }
                    $time = $checkin->format('H:i');

                    if ($checkin->gt($officeCheckin)) {
                        return '<span class="text-danger fw-bold">' . $time . '</span> <small class="text-danger">(Late Check-in)</small>';
                    }
                    return $time;
                })
                ->addColumn('time_out', function ($emp) use ($selectedDate) {
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);

                    if (!$detail || !$detail->checkout) {
                        return '-';
                    }
                    $checkoutTime = Carbon::parse($detail->checkout)->format('H:i:s');
                    $checkout = Carbon::parse($selectedDate . ' ' . $checkoutTime)->startOfMinute();

                    // $officeCheckout = Carbon::parse($selectedDate . ' ' . config('constant.OFFICE_CHECKOUT_TIME'));
                    $officeCheckout = Carbon::parse($selectedDate . ' ' . $detail->getOfficeCheckout())->startOfMinute();

                    // if ($detail->weekend_type_id == config('constant.Saturday')) {
                    //     $officeCheckout = Carbon::parse($selectedDate . ' ' . config('constant.OFFICE_FIELD_CHECKOUT_TIME'));
                    // }
                    $time = $checkout->format('H:i');

                    if ($checkout->lt($officeCheckout)) {
                        return '<span class="text-danger fw-bold">' . $time . '</span> <small class="text-danger">(Early Checkout)</small>';
                    }
                    return $time;
                })
                ->addColumn('hours_worked', function ($emp) use ($selectedDate) {
                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    return $detail && $detail->worked_hours
                        ? number_format($detail->worked_hours, 2)
                        : '-';
                })
                ->addColumn('remarks', function ($emp) use ($selectedDate, $dateObj) {
                    if ($dateObj->isFuture()) {
                        return 'Future Date';
                    }

                    $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($emp->id, $selectedDate);
                    if ($detail && ($detail->checkin || $detail->checkout)) {
                        return 'Present';
                    }

                    if ($this->isOnApprovedLeave($emp->id, $selectedDate)) {
                        return 'On Leave';
                    }

                    if ($this->isOnApprovedTravel($emp->id, $selectedDate)) {
                        return 'On Travel';
                    }

                    if ($dateObj->isWeekend()) {
                        return '<span class="text-secondary fw-bold">Weekend</span>';
                    }

                    return 'Absent';
                })
                ->addColumn('action', function ($emp) use ($selectedDate, $isToday) {
                    if ($isToday) {
                        return '<span class="text-muted">-</span>';
                    }

                    if (!auth()->user()->can('edit_daily_attendance')) {
                        return '<span class="text-muted">-</span>';
                    }

                    $date = Carbon::parse($selectedDate);

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
                ->rawColumns(['action', 'remarks', 'time_in', 'time_out'])
                ->make(true);
        }

        $data = [
            'offices' => $this->officeRepo->getActiveOffices(),
            'can_edit_attendance' => auth()->user()->can('edit_daily_attendance'),
        ];

        return view('EmployeeAttendance::DailyAttendance.index', $data);
    }

    private function isOnApprovedTravel(int $employeeId, string $date): bool
    {
        return \Modules\TravelRequest\Models\TravelRequest::query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->where(function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId)
                    ->orWhereHas('requester', function ($rq) use ($employeeId) {
                        $rq->where('employee_id', $employeeId);
                    });
            })
            ->whereDate('departure_date', '<=', $date)
            ->whereDate('return_date', '>=', $date)
            ->exists();
    }

    private function isOnApprovedLeave(int $employeeId, string $date): bool
    {
        return \Modules\LeaveRequest\Models\LeaveRequest::query()
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->whereHas('requester', function ($rq) use ($employeeId) {
                $rq->where('employee_id', $employeeId);
            })
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
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

        $employee = Employee::find($employeeId);
        $office = $employee->latestTenure?->office;

        $detail = $this->attendanceDetailRepo->getDetailByEmployeeAndDate($employeeId, $date);

        $officeHours = [
            'office_checkin_time' => $detail?->office_checkin_time ?? $office?->getOfficeCheckinTime() ?? config('constant.OFFICE_CHECKIN_TIME'),
            'office_checkout_time' => $detail?->office_checkout_time ?? $office?->getOfficeCheckoutTime() ?? config('constant.OFFICE_CHECKOUT_TIME'),
        ];

        if ($detail) {
            $detail->update(array_merge([
                'checkin' => $checkin,
                'checkout' => $checkout,
            ], $officeHours));
        } else {
            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => date('n', strtotime($date)),
                    'year' => date('Y', strtotime($date)),
                ],
                ['created_by' => auth()->id()]
            );

            $detail = AttendanceDetail::create(array_merge([
                'attendance_master_id' => $attendance->id,
                'attendance_date' => $date,
                'checkin' => $checkin,
                'checkout' => $checkout,
                'created_by' => auth()->id(),
            ], $officeHours));
        }

        if ($checkin && $checkout) {
            $start = Carbon::parse($checkin)->startOfMinute();
            $end = Carbon::parse($checkout)->startOfMinute();
            $hours = $start->diff($end)->format('%H.%I');
            $detail->update(['worked_hours' => $hours]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
        ]);
    }
}
