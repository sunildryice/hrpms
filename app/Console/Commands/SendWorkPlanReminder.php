<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Project\Notifications\WorkPlanReminder;
use Carbon\Carbon;

class SendWorkPlanReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:send:workplan:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a weekly work plan reminder email and notification to all active employees.';

    public function __construct(protected EmployeeRepository $employees)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Sending weekly work plan update reminders...');

        $employees = $this->employees->activeEmployees();
        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        $reminder = new WorkPlanReminder($weekStart, $weekEnd);

        $sent = 0;

        foreach ($employees as $employee) {
            if (!empty($employee->user) && !empty($employee->user->email_address)) {
                $employee->user->notify($reminder);
                $sent++;
            }
        }

        $this->info("Work plan reminders sent to {$sent} employees.");

        return Command::SUCCESS;
    }
}