<?php

namespace Modules\LieuLeave\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;

class OffDayWorkController extends Controller
{
    public function __construct(
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
        protected AttendanceDetailRepository $attendanceDetails,
        protected AttendanceRepository $attendance,
    ) {}


    public function index(Request $request, $date)
    {
        $leaveDate = Carbon::parse($date);

        $userId = auth()->id();

        $presentDates = $this->getPresentDates($request, $leaveDate->year, $leaveDate->month);

        $availableOffDayWorkDates = $this->lieuLeaveBalance->getOffDayWorkAvailableDates(
            $userId,
            $leaveDate->copy()->subMonth(),
        );

        $availableOffDayWorkDates = $availableOffDayWorkDates->filter(function ($offDayWorkDate) use ($presentDates) {
            return in_array(
                Carbon::parse($offDayWorkDate)->format('Y-m-d'),
                array_map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                }, $presentDates)
            );
        });


        return response()->json([
            'status' => 'success',
            'data' => [
                'available_off_day_work_dates' => $availableOffDayWorkDates,
            ],
        ]);
    }

    public function getPresentDates(Request $request, $year, $month)
    {
        $attendance = $this->attendance->getAttendanceObject(
            auth()->user()->employee_id,
            $year,
            $month,
        )->load([
            'attendanceDetails' => function ($q) {
                $q->whereNotNull('checkin')
                    ->whereNotNull('checkout');
            }
        ]);

        return $attendance->attendanceDetails->pluck('attendance_date')->toArray();
    }
}
