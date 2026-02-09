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
        $date = Carbon::create($this->argument('date') ?: date('Y-m-d'));
        $this->info('Getting all attendance records.');

        $employees = $this->employees->get();
        foreach ($employees as $employee) {
            $attendanceLogs = $this->attendanceLog->where('employee_code', $employee->employee_code)
                ->whereDate('attendance_timestamp', $date->format('Y-m-d'))
                ->orderBy('attendance_timestamp', 'asc')
                ->get();
            if (count($attendanceLogs) > 0) {
                $checkIn = $attendanceLogs->first()->attendance_timestamp;
                $checkOut = NULL;
                if ($attendanceLogs->count() > 1) {
                    $checkOut = $attendanceLogs->last()->attendance_timestamp;
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

                $inputs = [
                    'checkin' => $checkIn,
                    'checkout' => $checkOut,
                ];
                if ($checkIn) {
                    $inputs['checkin_from'] = 'Device';
                }
                if ($checkIn) {
                    $inputs['checkout_from'] = 'Device';
                }

                $detail = $this->attendanceDetails->updateOrCreate([
                    'attendance_master_id' => $attendance->id,
                    'attendance_date' => $date->format('Y-m-d'),
                ], $inputs);
                $this->info('Attendance record updated for a employee ' . $employee->employee_code);
            }
        }


        $this->info('Setting all attendance records.');
    }
}
