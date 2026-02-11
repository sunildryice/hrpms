<?php

namespace Modules\EmployeeAttendance\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

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

                    if ($detail && $detail?->checkin || $detail?->checkout) {
                        return 'Present';
                    }
                    if ($date->isWeekend()) {
                        return 'Weekend';
                    }

                    // $officeId = $emp->latestTenure?->office_id;
                    // if ($officeId) {
                    //     $holidays = $this->officeRepo->getHolidaysOneYear($officeId, $date->year);
                    //     if (in_array($date->format('Y-m-d'), $holidays)) {
                    //         return $emp->latestTenure?->is_annual_holiday ? 'Annual Holiday' : 'Holiday';
                    //     }
                    // }

                    return 'Absent';
                })
                ->make(true);
        }

        $data = ['offices' => $this->officeRepo->getActiveOffices(),];

        return view('EmployeeAttendance::DailyAttendance.index', $data);
    }
}