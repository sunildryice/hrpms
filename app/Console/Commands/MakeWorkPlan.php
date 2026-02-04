<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Carbon\Carbon;

class MakeWorkPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dryice:make:workplan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate work plans for employees';


    public function __construct(
        protected EmployeeRepository $employees,
        protected WorkPlanRepository $workPlans,

    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating work plans for employees...');
        $employees = $this->employees->activeEmployees();

        $weekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);

        for ($i = 0; $i < 4; $i++) {
            $currentWeekStart = $weekStart->copy()->addWeeks($i);

            $weekEnd = $currentWeekStart->copy()->addDays(6);

            $fromDate = $currentWeekStart->format('Y-m-d');
            $toDate = $weekEnd->format('Y-m-d');

            $this->info("Processing Week: $fromDate to $toDate");

            foreach ($employees as $employee) {
                $existingWorkPlan = $this->workPlans->findByDateAndEmployee($fromDate, $employee->id);

                if (! $existingWorkPlan) {
                    $workPlanData = [
                        'employee_id' => $employee->id,
                        'from_date' => $fromDate,
                        'to_date' => $toDate,

                    ];
                    $this->workPlans->createWorkPlan($workPlanData);
                    $this->info("Created for: {$employee->getFullName()} Date: $fromDate");
                }
            }
        }

        $this->info('Work plan generation completed.');
        return Command::SUCCESS;
    }
}
