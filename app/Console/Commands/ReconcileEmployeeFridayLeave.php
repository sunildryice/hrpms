<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestDayRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;

class ReconcileEmployeeFridayLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reconcile-friday-leave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile employee friday leave balance';

    /**
     * @param LeaveRequestDayRepository $leaveRequestDays
     */
    public function __construct(
        LeaveRequestDayRepository $leaveRequestDays,
        OfficeRepository $offices
    )
    {
        parent::__construct();
        $this->leaveRequestDays = $leaveRequestDays;
        $this->offices = $offices;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->ask('Please enter year');
        $year =  in_array($year, range(2022,2023)) ? $year : date('Y');
        $month = $this->ask('Please enter month');
        $month =  in_array($month, range(1,12)) ? $month : date('m');

        $officeIds = $this->offices->select('id')->where('weekend_type', 1)->pluck('id')->toArray();

        $this->info($year .' - '. $month);
        $this->info('Getting all leave requests.');
        $leaveRequestDays = $this->leaveRequestDays->select(['*'])
            ->whereHas('leaveRequest', function ($query) use ($officeIds){
                $query->whereIn('office_id', $officeIds);
            })->whereYear('leave_date', $year)
            ->whereMonth('leave_date', $month)
            ->get();
        foreach ($leaveRequestDays as $leaveRequestDay){
            $weekDay = date('w', strtotime($leaveRequestDay->leave_date));
            if($weekDay == 5){
                if($leaveRequestDay->leave_duration == 7){
                    $leaveRequestDay->update(['leave_duration'=>5]);
                    $this->info($leaveRequestDay->leave_duration == 7);
                    $this->info($leaveRequestDay->leaveRequest->getLeaveNumber());
                }
            }
        }
        $this->info('All leave requests records are updated.');
    }
}
