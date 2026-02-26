<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Privilege\Repositories\UserRepository;
use Modules\Project\Models\TimeSheet;
use Modules\Privilege\Models\User;
use Carbon\Carbon;
use Modules\Project\Repositories\TimeSheetRepository;

class GenerateTimesheet extends Command
{
    protected $signature = 'timesheets:generate';

    protected $description = 'Create one timesheet per month for the current year for every user (status_id=1)';

    public function __construct(
        protected TimeSheetRepository $timeSheets,
        protected UserRepository      $users
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $year = date('Y');
        $month = date('m');
        $this->info("Creating timesheets for {$year} - {$month}");
        $users = $this->users->select(['id'])
            ->whereHas('employee')
            ->whereNotNull('activated_at')
            ->get();

        $this->info("Found {$users->count()} users. Generating timesheets for each users.");

        foreach ($users as $user) {
            if (date('d') <= 25) {
                $startDate = Carbon::now()->subMonthNoOverflow()->day(26);
                $endDate = Carbon::now()->day(25);
            } else {
                $startDate = Carbon::now()->day(26);
                $endDate = Carbon::now()->addMonthNoOverflow()->day(25);
            }
            $monthName = Carbon::create($endDate)->format('M');
            $month = Carbon::create($endDate)->format('m');

            $timeSheet = $this->timeSheets->getTimeSheetOfUserByYearAndMonth($user->id, $year, $month);

            $this->info($user->id);
            $this->info($year);
            $this->info($month);
            $this->info($timeSheet->id);
            if (!$timeSheet) {
                $upd = $this->timeSheets->create([
                    'year' => $year,
                    'month' => $month,
                    'month_name' => $monthName,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status_id' => 1,
                    'requester_id' => $user->id,
                    'approver_id' => null,
                    'updated_by' => null,
                ]);
                $this->info($upd);
            }
        }
        $this->info("Timesheet generated for {$year} - {$month} if not exists.");
    }
}
