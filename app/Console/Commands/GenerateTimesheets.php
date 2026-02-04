<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Project\Models\TimeSheet;
use Modules\Privilege\Models\User;
use Carbon\Carbon;

class GenerateTimesheets extends Command
{
    protected $signature = 'timesheets:generate
                            {--clear : Delete all existing timesheets first}';

    protected $description = 'Create one timesheet per month for the current year for every user (status_id=1)';

    public function handle()
    {
        // if ($this->option('clear') && $this->confirm('Delete ALL existing timesheets?', false)) {
        //     TimeSheet::truncate();
        //     $this->info("All timesheets cleared.");
        // }

        $year = now()->year;
        $this->info("Creating timesheets for year {$year}...");
        $users = User::get(['id']);
        if ($users->isEmpty()) {
            $this->error("No users found in the system.");
            return self::FAILURE;
        }
        $this->info("Found {$users->count()} users. Generating 12 timesheets for each...");
        foreach ($users as $user) {
            for ($month = 1; $month <= 12; $month++) {
                $monthName = Carbon::create($year, $month, 1)->format('M');

                $start = Carbon::create($year, $month, 26)->subMonthNoOverflow();
                $end = Carbon::create($year, $month, 25);

                TimeSheet::create([
                    'year' => $year,
                    'month' => $monthName,
                    'start_date' => $start,
                    'end_date' => $end,
                    'status_id' => 1,
                    'requester_id' => $user->id,         
                    'approver_id' => null,
                    'updated_by' => null,
                ]);
            }
        }
        $this->newLine(2);
        $this->info("Done. Created 12 timesheets for each of {$users->count()} users in year {$year}.");
    }
}