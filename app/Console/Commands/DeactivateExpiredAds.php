<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateExpiredAds extends Command
{
    protected $signature = 'ads:deactivate-expired';
    protected $description = 'Deactivate ads whose end date has passed';

    public function handle()
    {
        $count = Ad::where('status', true)
            ->whereDate('end_date', '<', now())
            ->update(['status' => false]);

        $this->info("Deactivated {$count} expired ads.");
    }
}
