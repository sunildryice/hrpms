<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('dryice:reconcile:last:duty:date')->dailyAt('01:00');
        $schedule->command('dryice:update:employee:leave')->dailyAt('01:10');
        $schedule->command('dryice:reconcile-employee-yearly-leave-earned-balance')->dailyAt('01:20');
        $schedule->command('dryice:update:attendance:working:hour')->dailyAt('01:30');
        $schedule->command('dryice:make:workplan')->weekly();
        $schedule->command('dryice:import:attendance')->dailyAt('09:06');
        $schedule->command('dryice:import:attendance')->dailyAt('10:06');
        $schedule->command('dryice:import:attendance')->hourlyAt(16);
        $schedule->command('dryice:import:attendance')->hourlyAt(46);
        $schedule->command('dryice:generate:timesheets')->dailyAt('02:00');
        $schedule->command('dryice:auto:submit:timesheet')->dailyAt('09:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
