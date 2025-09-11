<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Models\Employee;

class ResetPasswordExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:update-tenure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password expiry command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Getting all employees.');
        $employees = Employee::with(['latestTenure'])->get();

        foreach($employees as $employee) {
            $employee->update([
                'supervisor_id' => $employee->latestTenure->supervisor_id,
                'cross_supervisor_id' => $employee->latestTenure->cross_supervisor_id,
                'next_line_manager_id' => $employee->latestTenure->next_line_manager_id,
            ]);
            $this->info('Employee '. $employee->getFullName() .' is updated.');
        }

        $this->info('Setting all employee records.');
    }
}
