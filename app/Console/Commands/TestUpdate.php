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

        $this->info('Attendance are updated.');
    }
}
