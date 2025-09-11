<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestDayRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;

class ReconcileEmployeeLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reconcile-employee-leave-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile employee leave balance';

    /**
     * @param EmployeeRepository $employees
     */
    public function __construct(
        EmployeeRepository $employees,
        FiscalYearRepository $fiscalYears,
        LeaveRepository $leaves,
        LeaveRequestDayRepository $leaveRequestDays,
        LeaveRequestRepository $leaveRequests
    )
    {
        parent::__construct();
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->leaves = $leaves;
        $this->leaveRequestDays = $leaveRequestDays;
        $this->leaveRequests = $leaveRequests;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->ask('Please enter year');
        $year =  in_array($year, range(2022, 2024)) ? $year : date('Y');
        $month = $this->ask('Please enter month');
        $month =  in_array($month, range(1,12)) ? $month : date('m');
        $this->info('Getting all employees.');
        $this->info($year .' - '. $month);

        $employees = $this->employees->select(['*'])
            ->whereNotNUll('activated_at')
            ->whereHas('user')
            ->get();

        foreach ($employees as $employee)
        {
            $this->info($employee->getFullNameWithCode());
            $this->leaves->reconcileEmployeeLeave($employee, $year, $month);
            $this->info('Leave balance is updated for '.$employee->getFullName());
        }

        $this->info('Leave balance is updated for all active employees');
    }
}
