<?php

namespace App\Console\Commands;

use App\Helper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailDonorRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\Master\Repositories\DonorCodeRepository;

class RecalculateAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:recalculate:attendance {employeeCode} {year} {month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        protected EmployeeRepository $employee,
        protected AttendanceRepository $attendance,
        protected AttendanceDetailRepository $attendanceDetail,
        protected AttendanceDetailDonorRepository $attendanceDonor,
        protected DonorCodeRepository $donor,
        protected Helper $helper,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $employeeCode = $this->argument('employeeCode');
        $year = $this->argument('year');
        $month = $this->argument('month');
        try {
            $employee = $this->employee->getEmployeeByCode($employeeCode);
            $attendance = $this->attendance->getAttendanceObject($employee->id, $year, $month);
            foreach ($attendance->attendanceDetails as $attendanceDetail) {
                foreach ($attendanceDetail->donors as $attendanceDonor) {
                    $inputs = [
                        'attendanceId' => $attendance->id,
                        'attendanceDate' => $attendanceDetail->attendance_date,
                        'donorId' => $attendanceDonor->donor_id,
                        'chargedHours' => $attendanceDonor->worked_hours,
                    ];
                    $attendanceId = $inputs['attendanceId'];
                    $attendanceDate = $inputs['attendanceDate'];
                    $donorId = $inputs['donorId'];
                    $chargedHours = $inputs['chargedHours'];

                    $attendanceDetail = $this->attendanceDetail->firstOrCreate([
                        'attendance_master_id' => $attendanceId,
                        'attendance_date' => $attendanceDate,
                    ]);

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

                        $worked_hours = $this->attendanceDonor->getTotalWorkedHours($attendanceDetail->id);
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
                        continue;
                        $chargedHours = '0'; // string 0
                    }

                    if ($chargedHours != null && ((int) $chargedHours) >= 0) {

                        if ($attendanceDetail->checkin && $attendanceDetail->checkout) {

                            $attendanceDetailDonor = $this->attendanceDonor->updateOrCreate([
                                'attendance_detail_id' => $attendanceDetail->id,
                                'donor_id' => $donorId,
                            ], [
                                'worked_hours' => $chargedHours,
                            ]);
                            $start = new Carbon($attendanceDetail->checkin);
                            $start->startOfMinute();
                            $end = new Carbon($attendanceDetail->checkout);
                            $end->startOfMinute();
                            $interval = round(floatval($start->diff($end)->format('%H.%I')), 2);
                            $worked_hours = round(floatval($this->attendanceDonor->getTotalWorkedHours($attendanceDetail->id)), 2);

                            if ($worked_hours > $interval) {
                                DB::rollBack();

                                return response()->json(['failure' => 'Time cannot exceed interval between \'Time In\' and \'Time Out\' .'], 400);
                            } else {
                                $unrestricted_hours = $this->helper->getHourDiff($interval, $worked_hours);
                                $attendanceDetail->update(['unrestricted_hours' => $unrestricted_hours, 'charged_hours' => $worked_hours]);
                            }
                        } else {
                            continue;
                        }
                    }
                }

            }

        } catch (QueryException $e) {
            $this->error($e);
        }

    }
}
