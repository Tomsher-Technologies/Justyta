<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VendorSubscription;
use App\Notifications\MembershipExpiryReminderNotification;
use Carbon\Carbon;

class SendMembershipExpiryReminders extends Command
{
    protected $signature = 'membership:send-expiry-reminders';
    protected $description = 'Send membership expiry reminders to vendors';

    public function handle()
    {
        $targetDate = Carbon::now()->addDays(3)->startOfDay();

        $subscriptions = VendorSubscription::whereDate('subscription_end', $targetDate)
            ->where('status', 'active')
            ->with('vendor.user')
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->vendor->user ?? null;

            if ($user && $user->banned == 0) {
                $user->notify(new MembershipExpiryReminderNotification(Carbon::parse($subscription->subscription_end)));
            }
        }

        $this->info("Reminders sent to " . $subscriptions->count() . " vendor(s).");
    }
}