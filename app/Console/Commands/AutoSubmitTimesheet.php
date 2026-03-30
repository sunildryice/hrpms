<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Project\Models\TimeSheetLog;
use Modules\Project\Notifications\TimeSheetSubmitted;
use Modules\Project\Repositories\TimeSheetRepository;

class AutoSubmitTimesheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:auto:submit:timesheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command submits timesheet automatically for a staffs who left the office';

    /**
     * @param EmployeeRepository $employees
     * @param TimeSheetRepository $timesheets
     */
    public function __construct(
        protected EmployeeRepository  $employees,
        protected TimeSheetRepository $timesheets,
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
        $employees = $this->employees->select(['*'])->whereDate('last_working_date', '<', now())->get();
        foreach ($employees as $employee) {
            $this->info('Timesheet submission for ' . $employee->getFullName() .' started.');
            $lineManagerId = $employee->supervisor->user->id;
            $timesheets = $this->timesheets->select(['*'])
                ->whereRequesterId($employee->user->id)
                ->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
                ->get();
            foreach ($timesheets as $timesheet) {
                $timesheet->update([
                    'approver_id' => $lineManagerId,
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                    'updated_by' => $timesheet->requester->id,
                    'updated_at' => now(),
                ]);

                $timesheet->logs()->create([
                    'user_id' => $timesheet->requester->id,
                    'log_remarks' => 'Timesheet submitted for approval.',
                    'status_id' =>  config('constant.SUBMITTED_STATUS'),
                ]);

                if ($timesheet->approver) {
                    $timesheet->approver->notify(new TimeSheetSubmitted($timesheet));
                }
            }
            $this->info('Timesheet submitted for ' . $employee->getFullName());
        }
    }
}
