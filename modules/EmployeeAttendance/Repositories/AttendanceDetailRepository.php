<?php

namespace Modules\EmployeeAttendance\Repositories;

use App\Helper;
use App\Repositories\Repository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Models\AttendanceDetail;
use Modules\EmployeeAttendance\Models\AttendanceDetailDonor;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\HolidayRepository;
use Modules\Master\Repositories\OfficeRepository;

class AttendanceDetailRepository extends Repository
{
    public function __construct(
        AttendanceDetail $attendanceDetail,
        protected OfficeRepository $office,
        protected DonorCodeRepository $donor,
        protected HolidayRepository $holidays,
        protected Helper $helper
    ) {
        $this->model = $attendanceDetail;
    }

    public function getDetail($attendanceId, $date): ?AttendanceDetail
    {
        return $this->model->where('attendance_master_id', $attendanceId)
            ->where('attendance_date', $date)->first();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceDetail = $this->model->create($inputs);
            DB::commit();

            return $attendanceDetail;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $attendanceDetail = $this->model->findOrFail($id);
            $attendanceDetail->fill($inputs)->save();
            DB::commit();

            return $attendanceDetail;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $attendanceDetail = $this->model->findOrFail($id);
            $attendanceDetail->delete();
            DB::commit();

            return $attendanceDetail;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function getCheckInTime($attendanceId, $date, $format = false)
    {
        $checkInTime = $this->model->where('attendance_master_id', '=', $attendanceId)->where('attendance_date', '=', $date)->first()?->getCheckinTime();

        if ($checkInTime != null) {
            if ($format == true) {
                return new DateTime($checkInTime);
            } else {
                return $checkInTime;
            }
        } else {
            return null;
        }
    }

    public function getCheckOutTime($attendanceId, $date, $format = false)
    {
        $checkOutTime = $this->model->where('attendance_master_id', '=', $attendanceId)->where('attendance_date', '=', $date)->first()?->getCheckoutTime();

        if ($checkOutTime != null) {
            if ($format == true) {
                return new DateTime($checkOutTime);
            } else {
                return $checkOutTime;
            }
        } else {
            return null;
        }
    }

    public function getWorkedHours($attendanceId, $date)
    {
        $attendanceDetail = $this->model->where('attendance_master_id', '=', $attendanceId)->where('attendance_date', '=', $date)->first();

        if ($attendanceDetail == null) {
            return 0;
        }

        $checkin = $attendanceDetail->checkin;
        $checkout = $attendanceDetail->checkout;

        if (($checkin != null || $checkin != 0 || $checkin != '') && ($checkout != null || $checkout != 0 || $checkout != '')) {

            $start = new Carbon($attendanceDetail->checkin);
            $start->startOfMinute();
            $end = new Carbon($attendanceDetail->checkout);
            $end->startOfMinute();
            $interval = $start->diff($end)->format('%H.%I');

        } else {
            $interval = 0;
        }

        return $interval;
    }

    public function getChargedHours($attendanceId, $date)
    {
        $attendanceDetail = $this->model->where('attendance_master_id', '=', $attendanceId)
            ->where('attendance_date', '=', $date)->first();

        if ($attendanceDetail == null) {
            return 0;
        }

        $charged_hours = $attendanceDetail->getChargedHours();
        $unrestricted_hours = $attendanceDetail->getUnrestrictedHours();
        // $chargedHours = $charged_hours + $unrestricted_hours;
        //
        // $whole = floor($chargedHours);
        // $fraction = $chargedHours - $whole;
        // if ($fraction > 0.6) {
        //     $fraction = $fraction + 0.4;
        // }
        // $chargedHours = floatval($whole + $fraction);

        $totalMinutes = $this->helper->convertToMinutes($charged_hours) + $this->helper->convertToMinutes($unrestricted_hours);
        $chargedHours = $this->helper->convertToHourMinute($totalMinutes);

        // $testMin1 = $this->helper->convertToMinutes(6.52);
        // $thisMin2 = $this->helper->convertToMinutes(0.08);
        // $testMin = $testMin1 + $thisMin2;
        // $testHours = $this->helper->convertToHourMinute($testMin);
        // if ($date == '2025-02-03') {
        //     dd($testMin, $testHours, $testMin1, $thisMin2);
        // }

        return round($chargedHours, 2);
    }

    public function getUnrestrictedHours($attendanceId, $date)
    {
        $attendanceDetail = $this->model->where('attendance_master_id', '=', $attendanceId)->where('attendance_date', '=', $date)->first();

        if ($attendanceDetail == null) {
            return floatval(0);
        }

        return round($attendanceDetail->unrestricted_hours, 2) ?? 0;
    }

    public function getId($attendanceId, $date)
    {
        $attendanceDetail = $this->model->where('attendance_master_id', '=', $attendanceId)->where('attendance_date', '=', $date)->first();

        if ($attendanceDetail == null) {
            return null;
        }

        return $attendanceDetail->id;

    }

    public function getAttendanceDetail($attendanceId)
    {
        $attendance = Attendance::find($attendanceId);

        $total_days = cal_days_in_month(CAL_GREGORIAN, $attendance->month, $attendance->year);
        $dates_month = [];

        $year = $attendance->year;
        $month = $attendance->month;

        for ($i = 1; $i <= $total_days; $i++) {
            $mktime = mktime(0, 0, 0, $month, $i, $year);
            $date = date('Y-m-d', $mktime);
            $dates_month[$i] = $date;
        }

        $holidays = $this->office->getHolidaysOneYear($attendance->office->id, $attendance->year);
        $weekends = $this->office->getOfficeWeekends($attendance->office->id, $attendance->year);
        $annualHolidays = $this->office->getOfficeHolidays($attendance->office->id);

        if ($attendance->employee->gender == config('constant.GENDER_MALE')) {
            $femaleHolidays = $this->holidays
                ->where('only_female', '=', 1)
                ->pluck('holiday_date')
                ->map(function ($date) {
                    return $date->format('Y-m-d');
                })->toArray();

            $holidays = array_diff($holidays, $femaleHolidays);
            $annualHolidays = array_diff($annualHolidays, $femaleHolidays);
        }

        $dates_month_with_holidays = [];

        $leaveDates = [];
        $leaveRequests = $attendance->requester->getApprovedLeaveRequests();
        foreach ($leaveRequests as $leaveRequest) {
            foreach ($leaveRequest->leaveDays as $leaveDay) {
                array_push($leaveDates, [
                    'leave_date' => $leaveDay->leave_date,
                    'leave_mode' => $leaveDay->leaveMode->title,
                    'leave_mode_id' => $leaveDay->leave_mode_id,
                    'leave_type_id' => $leaveRequest->leaveType->id,
                    'leave_type_name' => $leaveRequest->leaveType->getLeaveName(),
                    'leave_type_basis' => $leaveRequest->leaveType->getLeaveBasis(),
                    'leave_abbreviation' => Helper::getLeaveAbbreviation($leaveRequest->leaveType->id, $leaveDay->leave_mode_id),
                ]);
            }
        }

        $travelDates = [];
        $travelRequests = $attendance->requester->getApprovedTravelRequests();
        foreach ($travelRequests as $travelRequest) {
            array_push($travelDates, Helper::getDatesBetween($travelRequest->departure_date, $travelRequest->return_date));
        }
        $travelDates = call_user_func_array('array_merge', $travelDates);

        foreach ($dates_month as $key => $date) {

            $attendance_detail_id = $this->getId($attendanceId, $date);
            $attendance_detail_donors = AttendanceDetailDonor::where('attendance_detail_id', $attendance_detail_id)->get();
            $donor_list = [];
            if ($attendance_detail_donors->isNotEmpty()) {
                foreach ($attendance_detail_donors as $attendance_detail_donor) {
                    array_push($donor_list, [
                        'donor_id' => $attendance_detail_donor->donor_id,
                        'worked_hours' => $attendance_detail_donor->worked_hours,
                    ]);
                }
            }

            array_push($dates_month_with_holidays, collect([
                'date' => $date,
                'holiday' => in_array($date, $holidays) ? true : false,
                'is_weekend' => in_array($date, $weekends) ? true : false,
                'is_annual_holiday' => in_array($date, $annualHolidays) ? true : false,
                'day' => $key,
                'month' => $attendance->month,
                'year' => $attendance->year,
                'day_name' => date('D', strtotime($date)),
                'month_name' => date('F', mktime(0, 0, 0, $attendance->month, 10)),
                'check_in_time' => $this->getCheckInTime($attendanceId, $date),
                'check_out_time' => $this->getCheckOutTime($attendanceId, $date),
                'worked_hours' => $this->getWorkedHours($attendanceId, $date),
                'hours_charged' => $this->getChargedHours($attendanceId, $date),
                'attendance_detail_id' => $this->getId($attendanceId, $date),
                'donor_list' => $donor_list,
                'unrestricted_hours' => $this->getUnrestrictedHours($attendanceId, $date),
                'disabled' => Helper::isGreaterThanCurrentDate($date),
                'in_travel' => in_array($date, $travelDates) ? true : false,
            ]));

            foreach ($leaveDates as $leaveDate) {
                if ($leaveDate['leave_date'] == $date) {
                    $dates_month_with_holidays[$key - 1]->put('leave', $leaveDate);
                }
            }
        }

        $dates = collect($dates_month_with_holidays)->map(function ($row) {
            return collect($row);
        });

        return $dates;
    }
}
