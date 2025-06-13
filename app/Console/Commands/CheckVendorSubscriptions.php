<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VendorSubscription;
use App\Notifications\SubscriptionExpiredNotification;
use Carbon\Carbon;

class CheckVendorSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expiry';
    protected $description = 'Suspend or cancel vendor memberships with expired subscriptions';

    public function handle()
    {
        $today = Carbon::today();
        $expiredSubscriptions = VendorSubscription::whereDate('subscription_end', '<', $today)
                                        ->where('status', 'active') // or your active status
                                        ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->status = 'expired';
            $subscription->save();
            if ($subscription->vendor && $subscription->vendor->user) {
                $subscription->vendor->user->notify(new SubscriptionExpiredNotification());
            }
        }

        $this->info("Expired subscriptions updated: $expired");
    }
}