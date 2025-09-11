<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;

class UpdateAttendanceWorkingHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:update:attendance:working:hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update attendance working hour';

    /**
     * @param AttendanceRepository $attendances
     */
    public function __construct(
        AttendanceRepository       $attendances,
        AttendanceDetailRepository $attendanceDetails
    )
    {
        parent::__construct();
        $this->attendances = $attendances;
        $this->attendanceDetails = $attendanceDetails;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting attendance.');
        $attendances = $this->attendances->select('*')
            ->with(['attendanceDetails'])
            ->where('status_id', '<>', config('constant.APPROVED_STATUS'))
            ->orderby('created_at', 'desc')
            ->get();

        foreach ($attendances as $attendance) {
            $this->info($attendance->id . ' is being updated.');
            foreach ($attendance->attendanceDetails as $attendanceDetail) {
//                $this->info($attendanceDetail->attendance_date);
                $checkin = $attendanceDetail->checkin;
                $checkout = $attendanceDetail->checkout;
                $interval = $totalWorkingMinutes = 0;

                if (($checkin != null || $checkin != 0 || $checkin != '') && ($checkout != null || $checkout != 0 || $checkout != '')) {
                    $start = new Carbon($attendanceDetail->checkin);
                    $start->startOfMinute();
                    $end = new Carbon($attendanceDetail->checkout);
                    $end->startOfMinute();
                    $interval = $start->diff($end)->format('%H.%I');
                    $hour = intval($interval);
                    $minuteFraction = $interval - $hour;
                    $totalWorkingMinutes = $hour * 60 + $minuteFraction * 100;
                }

                if ($interval != $attendanceDetail->worked_hours) {
                    $this->attendanceDetails->update($attendanceDetail->id, ['worked_hours' => $interval]);
                    $this->info('Attendance ' . $attendanceDetail->attendance_date . ' is updated.');
                }

                $totalDonorMinute = 0;
                foreach ($attendanceDetail->donors as $donorCharge) {
                    $hour = intval($donorCharge->worked_hours);
                    $minutes = round(($donorCharge->worked_hours - $hour) * 100);
                    $totalMinutes = $hour * 60 + $minutes;
                    $totalDonorMinute += $totalMinutes;
                }
                $donorHour = intval($totalDonorMinute / 60);
                $donorMinute = round($totalDonorMinute - $donorHour * 60);
                $donorMinuteFraction = sprintf('%02d', $donorMinute);
                $donorCharge = (float)($donorHour . '.' . $donorMinuteFraction);

                $totalUnrestrictedMinutes = round($totalWorkingMinutes - $totalDonorMinute);
                $unrestrictedHour = intval($totalUnrestrictedMinutes / 60);
                $unrestrictedMinute = round($totalUnrestrictedMinutes - $unrestrictedHour * 60);
                $unrestrictedMinuteFraction = sprintf('%02d', $unrestrictedMinute);
                $unrestrictedCharge = (float)($unrestrictedHour . '.' . $unrestrictedMinuteFraction);

                if ($donorCharge != $attendanceDetail->charged_hours) {
                    $this->attendanceDetails->update($attendanceDetail->id, ['charged_hours' => $donorCharge]);
                    $this->info('Attendance ' . $attendanceDetail->attendance_date . ' (donor charge) is updated.');
                }

                if ($unrestrictedCharge != $attendanceDetail->unrestricted_hours) {
                    $this->attendanceDetails->update($attendanceDetail->id, ['unrestricted_hours' => $unrestrictedCharge]);
                    $this->info('Attendance ' . $attendanceDetail->attendance_date . ' (unrestricted charge) is updated.');
                }
            }
        }
    }
}
