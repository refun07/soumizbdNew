<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Accounting\ProcessRecurringExpensesJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process recurring expenses daily at 2:00 AM
        $schedule->job(ProcessRecurringExpensesJob::class)
            ->dailyAt('02:00')
            ->description('Process and generate recurring expenses')
            ->withoutOverlapping();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
