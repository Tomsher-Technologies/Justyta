<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('ads:deactivate-expired')->daily();
        $schedule->command('check:vendor-expiry')->daily();
        $schedule->command('subscriptions:check-expiry')->daily();
        $schedule->command('membership:send-expiry-reminders')->everyMinute();
        $schedule->command('consultations:release-expired')->everyMinute();
        $schedule->command('queue:work --stop-when-empty')
                ->everyMinute()
                ->withoutOverlapping();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function scheduleTimezone()
    {
        return 'Asia/Dubai'; // or your timezone
    }
}
