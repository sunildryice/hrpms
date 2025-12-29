<?php

namespace Modules\EmployeeAttendance\Controllers;

use App\Helper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\EmployeeAttendance\Models\Attendance;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailDonorRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Requests\AttendanceDetail\StoreRequest;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Privilege\Repositories\UserRepository;

class AttendanceDetailController extends Controller
{
    public function __construct(
        protected AttendanceDetailRepository $attendanceDetail,
        protected Attendance $attendance,
        protected OfficeRepository $office,
        protected DonorCodeRepository $donor,
        protected AttendanceDetailDonorRepository $attendanceDetailDonor,
        protected LeaveRepository $leaves,
        protected Helper $helper,
        protected UserRepository $user,
        protected FiscalYearRepository $fiscalYears,
    ) {}

    public function index(Request $request)
    {
        return view('EmployeeAttendance::AttendanceDetail.index');
    }

    public function show(Request $request, $attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);
        $summary = $attendance->getTotalSummary();
        $donors = $this->donor->select(['*'])->get();

        $array = [
            'attendance' => $attendance,
            'attendanceId' => $attendanceId,
            'dates' => $this->attendanceDetail->getAttendanceDetail($attendanceId),
            'unrestrictedDonor' => $this->donor->getUnrestrictedDonor(),
            'donors' => $donors,
            // 'leaves'                        => $this->leaves->getEmployeeLeavesForCurrentFiscalYear($attendance->employee->id),
            'leaves' => $this->leaves->getMonthlyEmployeeLeaves($attendance->employee->id, $attendance->year, $attendance->month),
            'donor_charges' => $attendance->getDonorCharges(),
            'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
            'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
            'total_worked_hours' => $summary->get('total_worked_hours'),
            'total_charged_hours' => $summary->get('total_charged_hours'),
            'total_charged_percentage' => $summary->get('total_charged_percentage'),
        ];

        return view('EmployeeAttendance::AttendanceDetail.show', $array);
    }

    public function edit(Request $request, $attendanceId)
    {

        $attendance = $this->attendance->find($attendanceId);
        $dates = $this->attendanceDetail->getAttendanceDetail($attendanceId);
        $approvers = $this->user->getSupervisors(auth()->user());
        $summary = $attendance->getTotalSummary();

        $array = [
            'attendance' => $attendance,
            'attendanceId' => $attendanceId,
            'dates' => $dates,
            'donors' => $this->donor->getActiveDonorCodes(),
            'unrestrictedDonor' => $this->donor->getUnrestrictedDonor(),
            'enabledDonors' => $this->donor->getEnabledDonorCodes(),
            // 'leaves'                        => $this->leaves->getEmployeeLeavesForCurrentFiscalYear($attendance->employee->id),
            'leaves' => $this->leaves->getMonthlyEmployeeLeaves($attendance->employee->id, $attendance->year, $attendance->month),
            'editable' => auth()->user()->can('submit', $attendance),
            'reviewers' => $this->user->permissionBasedUsers('review-employee-attendance'),
            'donor_charges' => $attendance->getDonorCharges(),
            'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
            'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
            'total_worked_hours' => $summary->get('total_worked_hours'),
            'total_charged_hours' => $summary->get('total_charged_hours'),
            'total_charged_percentage' => $summary->get('total_charged_percentage'),
            'approvers' => $approvers,
        ];

        return view('EmployeeAttendance::AttendanceDetail.edit', $array);
    }

    public function store(StoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $inputs = $request->validated();
            $attendanceId = $request->attendanceId;
            $attendanceDate = $request->attendanceDate;
            $donorId = $request->donorId;
            $chargedHours = $request->chargedHours;

            $attendanceDetail = $this->attendanceDetail->firstOrCreate([
                'attendance_master_id' => $attendanceId,
                'attendance_date' => $attendanceDate,
            ]);

            if ($request->checkInTime !== null) {
                $checkInTime = new DateTime($request->checkInTime);
                $checkOutTime = $this->attendanceDetail->getCheckOutTime($attendanceId, $attendanceDate, true);
                $inputs['checkin'] = $request->checkInTime;
                $this->attendanceDetail->update($attendanceDetail->id, $inputs);

                if ($checkOutTime != null) {
                    if ($checkInTime > $checkOutTime) {
                        DB::rollBack();

                        return response()->json(['failure' => '\'Time In\' must be less than \'Time Out\''], 400);
                    }
                }
            }

            if ($request->checkOutTime !== null) {
                $checkInTime = $this->attendanceDetail->getCheckInTime($attendanceId, $attendanceDate, true);
                $checkOutTime = new DateTime($request->checkOutTime);
                $inputs['checkout'] = $request->checkOutTime;
                $this->attendanceDetail->update($attendanceDetail->id, $inputs);

                if ($checkInTime != null) {
                    if ($checkOutTime < $checkInTime) {
                        DB::rollBack();
                        return response()->json(['failure' => '\'Time In\' must be less than \'Time Out\''], 400);
                    }
                }
            }

            $attendanceDetail = $this->attendanceDetail->find($attendanceDetail->id);
            if ($attendanceDetail->checkin && $attendanceDetail->checkout) {
                $start = new Carbon($attendanceDetail->checkin);
                $start->startOfMinute();
                $end = new Carbon($attendanceDetail->checkout);
                $end->startOfMinute();
                $interval = $start->diff($end)->format('%H.%I');

                $hour = intval($interval);
                $minuteFraction = $interval - $hour;
                $totalWorkingMinutes = $hour * 60 + $minuteFraction * 100;

                $worked_hours = $this->attendanceDetailDonor->getTotalWorkedHours($attendanceDetail->id);
                $donorHours = intval($worked_hours);
                $donorMinuteFraction = $worked_hours - $donorHours;
                $totalDonorMinutes = $donorHours * 60 + $donorMinuteFraction * 100;

                $totalUnrestrictedMinutes = round($totalWorkingMinutes - $totalDonorMinutes);
                $unrestrictedHour = intval($totalUnrestrictedMinutes / 60);
                $unrestrictedMinute = round($totalUnrestrictedMinutes - $unrestrictedHour * 60);
                $unrestrictedMinuteFraction = sprintf('%02d', $unrestrictedMinute);
                $unrestrictedCharge = (float) ($unrestrictedHour.'.'.$unrestrictedMinuteFraction);

                $this->attendanceDetail->update($attendanceDetail->id, ['unrestricted_hours' => $unrestrictedCharge, 'worked_hours' => $interval]);
            }

            if ($this->donor->getUnrestrictedDonor()?->id == $donorId) {
                $chargedHours = '0';
            }

            if ($chargedHours != null && ((int) $chargedHours) >= 0) {

                if ($attendanceDetail->checkin && $attendanceDetail->checkout) {

                    $attendanceDetailDonor = $this->attendanceDetailDonor->updateOrCreate([
                        'attendance_detail_id' => $attendanceDetail->id,
                        'donor_id' => $donorId,
                    ], [
                        'worked_hours' => $chargedHours,
                        'activities' => $inputs['activities'],
                        'project_id' => $inputs['project_id'] ?? null,
                    ]);
                    $start = new Carbon($attendanceDetail->checkin);
                    $start->startOfMinute();
                    $end = new Carbon($attendanceDetail->checkout);
                    $end->startOfMinute();
                    $interval = round(floatval($start->diff($end)->format('%H.%I')), 2);
                    $worked_hours = round(floatval($this->attendanceDetailDonor->getTotalWorkedHours($attendanceDetail->id)), 2);

                    if ($worked_hours > $interval) {
                        DB::rollBack();

                        return response()->json(['failure' => 'Time cannot exceed interval between \'Time In\' and \'Time Out\' .'], 400);
                    } else {
                        // $unrestricted = floatval($interval - $worked_hours);
                        // $whole = floor($unrestricted);
                        // $fraction = $unrestricted - $whole;
                        // if ($fraction > 0.6) {
                        //     $fraction = $fraction - 0.4;
                        // }
                        // $unrestricted_hours = floatval($whole + $fraction);
                        $unrestricted_hours = $this->helper->getHourDiff($interval, $worked_hours);
                        $attendanceDetail->update(['unrestricted_hours' => $unrestricted_hours, 'charged_hours' => $worked_hours]);
                    }
                } else {
                    DB::rollBack();

                    return response()->json(['failure' => 'Please set \'Time In\' and \'Time Out\' first.'], 400);
                }
            }
            DB::commit();
            $summary = $attendanceDetail->attendance->getTotalSummary();

            return response()->json([
                'success' => 'Success!',
                'worked_hours' => $attendanceDetail->getWorkedHours(),
                'total_worked_hours' => $summary->get('total_worked_hours'),
                'time_in_time_out_interval' => $this->attendanceDetail->getWorkedHours($attendanceDetail->attendance_master_id, $attendanceDetail->attendance_date),
                'current_attendance_detail_charged_hours' => $this->attendanceDetail->getChargedHours($attendanceDetail->attendance_master_id, $attendanceDetail->attendance_date),
                'current_attendance_detail_unrestricted_hours' => $attendanceDetail->getUnrestrictedHours(),
                'attendance_detail' => $attendanceDetail,
                'donor_charges' => $attendanceDetail->attendance->getDonorCharges(),
                'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
                'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
                'total_charged_hours' => $summary->get('total_charged_hours'),
                'total_charged_percentage' => $summary->get('total_charged_percentage'),

            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);

            return response()->json([
                'success' => 'error',
                'message' => 'Failed to update attendance',
            ], 422);
        }
    }

    public function print(Request $request, $attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);
        $summary = $attendance->getTotalSummary();

        $array = [
            'attendance' => $attendance,
            'attendanceId' => $attendanceId,
            'dates' => $this->attendanceDetail->getAttendanceDetail($attendanceId),
            'unrestrictedDonor' => $this->donor->getUnrestrictedDonor(),
            'donors' => $this->donor->getActiveDonorCodes(),
            // 'leaves'                        => $this->leaves->getEmployeeLeavesForCurrentFiscalYear($attendance->employee->id),
            'leaves' => $this->leaves->getMonthlyEmployeeLeaves($attendance->employee->id, $attendance->year, $attendance->month),
            'donor_charges' => $attendance->getDonorCharges(),
            'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
            'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
            'total_worked_hours' => $summary->get('total_worked_hours'),
            'total_charged_hours' => $summary->get('total_charged_hours'),
            'total_charged_percentage' => $summary->get('total_charged_percentage'),
        ];

        return view('EmployeeAttendance::AttendanceDetail.print', $array);
    }

    public function view(Request $request, $attendanceId)
    {
        $attendance = $this->attendance->find($attendanceId);
        $summary = $attendance->getTotalSummary();
        $dates = $this->attendanceDetail->getAttendanceDetail($attendanceId);

        $array = [
            'attendance' => $attendance,
            'attendanceId' => $attendanceId,
            'dates' => $dates,
            'donors' => $this->donor->getActiveDonorCodes(),
            'unrestrictedDonor' => $this->donor->getUnrestrictedDonor(),
            'leaves' => $this->leaves->getMonthlyEmployeeLeaves($attendance->employee->id, $attendance->year, $attendance->month),
            'donor_charges' => $attendance->getDonorCharges(),
            'total_unrestricted_hours' => $summary->get('total_unrestricted_hours'),
            'total_unrestricted_percentage' => $summary->get('total_unrestricted_percentage'),
            'total_worked_hours' => $summary->get('total_worked_hours'),
            'total_charged_hours' => $summary->get('total_charged_hours'),
            'total_charged_percentage' => $summary->get('total_charged_percentage'),
        ];

        return view('EmployeeAttendance::AttendanceDetail.view', $array);
    }

    public function update(Request $request)
    {
        sleep(1.5);

        return redirect()->back()->withSuccessMessage('Attendance Updated Successfully');
    }

    public function recalculate($attendanceId)
    {
        $attendance = $this->attendance->with('employee')->find($attendanceId);
        if (! in_array($attendance->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])) {
            return redirect()->back()->withSuccessMessage('Attendance cannot be recalculated');
        }
        Artisan::call('dryice:recalculate:attendance', [
            'employeeCode' => $attendance->employee->employee_code,
            'year' => $attendance->year,
            'month' => $attendance->month,
        ]);

        return redirect()->back()->withSuccessMessage('Attendance Updated Successfully');
    }
}
