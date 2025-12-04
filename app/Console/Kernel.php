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
        $schedule->command('ads:deactivate-expired')->daily()->description('Deactivate expired ads');
        $schedule->command('check:vendor-expiry')->daily()->description('Check vendor expiry');
        $schedule->command('subscriptions:check-expiry')->daily()->description('Check subscription expiry');
        $schedule->command('membership:send-expiry-reminders')->everyMinute()->description('Send membership expiry reminders');
        $schedule->command('consultations:release-expired')->everyMinute()->description('Release expired consultations');
        $schedule->command('consultation:auto-cancel')->everyMinute()->description('Auto cancel consultations');
        $schedule->command('queue:work --stop-when-empty')
                ->everyMinute()->description('Process the queue');
                // ->withoutOverlapping();

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
