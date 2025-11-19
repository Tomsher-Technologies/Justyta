<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('ads:deactivate-expired')->daily();
Schedule::command('check:vendor-expiry')->daily();
Schedule::command('subscriptions:check-expiry')->daily();
Schedule::command('membership:send-expiry-reminders')->everyMinute();
Schedule::command('consultations:release-expired')->everyMinute();
Schedule::command('consultation:auto-cancel')->everyMinute();
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();


