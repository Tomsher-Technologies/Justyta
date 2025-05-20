<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Subscription Has Expired')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We want to inform you that your subscription has expired.')
            ->line('Please renew your membership to continue accessing your dashboard and services.')
            // ->action('Renew Now', url('/vendor/subscription'))
            ->line('Thank you for using our service!');
    }
}
