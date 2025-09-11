<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveLogRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;

class ReconcileEmployeeLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reconcile:employee:leave {employee}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update employee master leave for a selected employee.';

    /**
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param LeaveRepository $employeeLeaves
     * @param LeaveTypeRepository $leaveTypes
     */
    public function __construct(
        protected EmployeeRepository   $employees,
        protected FiscalYearRepository $fiscalYears,
        protected LeaveRepository      $employeeLeaves,
        protected LeaveTypeRepository  $leaveTypes
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
        $employeeCode = $this->argument('employee');
        $employee = $this->employees->findByField('employee_code', $employeeCode);

        $this->info('Leave records of '. $employee->getFullName() .' are being updated.');
        $previousMonth = 0;
        foreach(range(1, date('m')) as $month)
        {
            $month = sprintf("%02d", $month);
            $reportedDate = date('Y-' . $month . '-01');
            $this->info('Report date => '. $reportedDate);

            $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                ->where('end_date', '>=', date('Y-m-d'))
                ->first();


            $previousYear = collect();
            if ($month == '01') {
                $pyDate = Carbon::now()->startOfYear()->subMonth()->format('Y-m-d');
                $previousYear = $this->fiscalYears->where('start_date', '<=', $pyDate)
                    ->where('end_date', '>=', $pyDate)
                    ->first();
            }
            $this->employeeLeaves->generateEmployeeLeave($employee, $reportedDate, $fiscalYear, $previousYear);
            $this->employeeLeaves->reconcileEmployeeLeave($employee, date('Y'), $month);
        }
        $this->info('Leave records of '. $employee->getFullName() .' are updated.');
    }
}
