<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Employee\Repositories\LeaveLogRepository;
use Modules\Employee\Repositories\LeaveRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\LeaveTypeRepository;

class UpdateConsultantLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:update:consultant:leave {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update consultant master leave.';

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
        $month = $this->argument('month') ?: date('m');
        $reportedDate = date('Y-' . $month . '-01');
        $this->info('Getting all consultants.');
        $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        $previousYear = collect();
        if ($month == '01') {
            $pyDate = date('Y-m-d', strtotime('-1 month'));
            $previousYear = $this->fiscalYears->where('start_date', '<=', $pyDate)
                ->where('end_date', '>=', $pyDate)
                ->first();
        }

        $this->info($fiscalYear->start_date);
        $this->info($fiscalYear->end_date);

        $employees = $this->employees->getActiveConsultants();

        foreach ($employees as $employee) {
            if($employee->consultantLeave) {
                $this->call('dryice:reconcile:consultant:leave', ['employee' => $employee->ste_code]);
                $this->info($employee->ste_code);
            }
        }
        $this->info('Leave of all employees are updated.');
    }
}
