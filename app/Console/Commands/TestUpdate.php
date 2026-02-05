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
        protected EmployeeRepository         $employees,
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
        $this->info('Getting employees.');

        $employees  = $this->employees->getAllEmployees();
        foreach($employees as $employee){
            if(!$employee->user) {
                $employee->user()->create([
                    'full_name'=>$employee->full_name,
                    'email_address'=>$employee->official_email_address,
                    'password'=>bcrypt($employee->official_email_address .rand(1000, 9999)),
                ]);
                $this->info($employee->employee_code);
            }
        }

        $this->info('Users are created.');
    }
}
