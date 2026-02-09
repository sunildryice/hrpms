<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Models\Employee;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EmployeeExit\Repositories\ExitHandOverNoteRepository;

class ReconcileLastDateOfDuty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:reconcile:last:duty:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update last date of duty record of an exit employee.';

    /**
     * @param EmployeeRepository $employees
     * @param ExitHandOverNoteRepository $exitHandovers
     */
    public function __construct(
        EmployeeRepository   $employees,
        ExitHandOverNoteRepository  $exitHandovers
    )
    {
        parent::__construct();
        $this->employees = $employees;
        $this->exitHandovers = $exitHandovers;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting all employee exit handovers.');
        $employeeExits = $this->exitHandovers->with(['employee'])->get();

        foreach($employeeExits as $exit) {
            if(!$exit->employee->last_working_date){
                $exit->employee->update([
                    'last_working_date' => $exit->last_duty_date
                ]);
                $this->info('Last working date of employee '. $exit->employee->getFullName() .' is updated.');
            }
        }
        $this->info('Setting all employee last date of duty of exit employee.');
    }
}
