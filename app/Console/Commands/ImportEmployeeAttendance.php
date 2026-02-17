<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;

class ImportEmployeeAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:import:attendance {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import attendance data of employees';

    public function __construct(
        protected AttendanceRepository       $attendances,
        protected AttendanceLog              $attendanceLog,
        protected AttendanceDetailRepository $attendanceDetails,
        protected EmployeeRepository         $employees
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::create($this->argument('date') ?: date('Y-m-d'));
        $this->info('Getting all attendance records.');

        $employees = $this->employees->getActiveEmployees();
        foreach ($employees as $employee) {
            $attendanceLogs = $this->attendanceLog->where('employee_code', $employee->employee_code)
                ->whereDate('attendance_timestamp', $date->format('Y-m-d'))
                ->orderBy('attendance_timestamp', 'asc')
                ->get();
            $attendanceDetail = $this->attendanceDetails->getDetailByEmployeeAndDate($employee->employee_code, $date->format('Y-m-d'));
            $manualCheckInExists = $attendanceDetail ? ($attendanceDetail->checkin && $attendanceDetail->checkin_from != 'Device' ? true : false) : false;

            if (count($attendanceLogs) > 0) {
                $attendanceInputs = [];
                if ($manualCheckInExists) {
                    $checkIn = $attendanceDetail->checkin;
                    $checkOut = $attendanceLogs->last()->attendance_timestamp;
                    $attendanceInputs['checkout'] = $checkOut;
                    $attendanceInputs['checkout_from'] = 'Device';
                } else {
                    $checkIn = $attendanceLogs->first()->attendance_timestamp;
                    $checkOut = NULL;
                    if ($attendanceLogs->count() > 1) {
                        $checkOut = $attendanceLogs->last()->attendance_timestamp;
                    }
                    $attendanceInputs['checkin'] = $checkIn;
                    $attendanceInputs['checkout'] = $checkOut;
                    $attendanceInputs['checkin_from'] = 'Device';
                    $attendanceInputs['checkout_from'] = 'Device';
                }

                $attendance = $this->attendances->getAttendanceObject($employee->id, $date->year, $date->month);
                if (!$attendance) {
                    $inputs = [
                        'employee_id' => $employee->id,
                        'department_id' => $employee->latestTenure->department_id,
                        'designation_id' => $employee->latestTenure->designation_id,
                        'office_id' => $employee->latestTenure->office_id,
                        'duty_station_id' => $employee->latestTenure->duty_station_id,
                        'year' => $date->year,
                        'month' => $date->month,
                        'requester_id' => auth()->id(),
                        'updated_by' => auth()->id(),
                        'status_id' => config('constant.CREATED_STATUS') ?? 1,
                        'donor_codes' => '',
                    ];
                    $attendance = $this->attendances->create($inputs);
                }

                $detail = $this->attendanceDetails->updateOrCreate([
                    'attendance_master_id' => $attendance->id,
                    'attendance_date' => $date->format('Y-m-d'),
                ], $attendanceInputs);
                $this->info('Attendance record updated for a employee ' . $employee->employee_code);
            }
        }

        foreach ($employees as $employee) {
            $attendanceDetail = $this->attendanceDetails->getDetailByEmployeeAndDate($employee->employee_code, $date->format('Y-m-d'));
            if ($attendanceDetail) {
                $checkIn = $attendanceDetail->checkin;
                $checkOut = $attendanceDetail->checkout;
                if ($checkIn && $checkOut) {
                    $checkIn = Carbon::parse($checkIn)->startOfMinute();
                    $checkOut = Carbon::parse($checkOut)->startOfMinute();

                    $workedHours = $checkIn->diff($checkOut)->format('%H.%I');
                    $attendanceDetail->update(['worked_hours' => $workedHours]);

                    $this->info('Work hour is updated for '. $employee->getFullName());
                }
            }
        }
        $this->info('Setting all attendance records.');
    }
}
