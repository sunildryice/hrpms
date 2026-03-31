<?php

namespace Modules\LieuLeave\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\LieuLeave\Repositories\LieuLeaveBalanceRepository;
use Modules\OffDayWork\Repositories\OffDayWorkRepository;

class OffDayWorkController extends Controller
{
    public function __construct(
        protected LieuLeaveBalanceRepository $lieuLeaveBalance,
        protected AttendanceDetailRepository $attendanceDetails,
        protected AttendanceRepository $attendance,
        protected OffdayWorkRepository $offDayWorks,
    ) {}


    public function index(Request $request, $date)
    {
        $leaveDate = Carbon::parse($date);
        $userId = auth()->user()->id;
        $startDate = $leaveDate->copy()->subMonthNoOverflow();
        $checkLieuLeaveApplied = $this->lieuLeaveBalance->checkLieuRequestOnLeaveMonthByDate($userId, $leaveDate);

        $lieuLeaveAvailableDates = [];
        if(!$checkLieuLeaveApplied) {
            $offDayWorkDates = $this->offDayWorks->select('date')
                ->where('requester_id', $userId)
                ->whereBetween('date', [$startDate, $leaveDate])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->pluck('date')->toArray();

            $lieuLeaveAvailableDates = $this->lieuLeaveBalance->select(['earned_date'])
                ->where('user_id', $userId)
                ->whereNull('lieu_leave_request_id')
                ->whereIn('earned_date', $offDayWorkDates)
                ->pluck('earned_date')->toArray();
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'available_off_day_work_dates' => $lieuLeaveAvailableDates,
            ],
        ]);
    }

    public function getPresentDates(Request $request, $year, $month)
    {
        $attendance = $this->attendance->getAttendanceObject(
            auth()->user()->employee_id,
            $year,
            $month,
        )?->load([
            'attendanceDetails' => function ($q) {
                $q->whereNotNull('checkin')
                    ->whereNotNull('checkout');
            }
        ]);

        return $attendance->attendanceDetails->pluck('attendance_date')->toArray();
    }
}
