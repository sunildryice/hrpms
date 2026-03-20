<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceDetailRepository;
use Modules\EmployeeAttendance\Repositories\AttendanceRepository;
use Modules\MaintenanceRequest\Repositories\MaintenanceRequestRepository;

class TestUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:test:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test update';

    /**
     * @param AttendanceDetailRepository $attendanceDetails
     */
    public function __construct(
        protected AttendanceDetailRepository $attendanceDetails,
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
        $this->info('Getting attendance details.');

        $attendanceDetails = $this->attendanceDetails->get();
        foreach ($attendanceDetails as $detail) {
            if ($detail->attendance) {
                $detail->update([
                    'office_id' => $detail->attendance->employee->office_id,
                    'weekend_type_id' => $detail->office ? $detail->office->weekend_type : null,
                ]);
                $this->info($detail->id);
            }
        }

        $this->info('Attendance are updated.');
    }
}
