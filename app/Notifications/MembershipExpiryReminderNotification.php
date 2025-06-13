<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class MembershipExpiryReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $expiryDate;

    public function __construct(Carbon $expiryDate)
    {
        $this->expiryDate = $expiryDate->format('d M Y');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Membership Expiry Reminder')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("This is a friendly reminder that your membership is set to expire on **{$this->expiryDate}**.")
            ->line("To avoid service interruptions, please renew your subscription before the expiry date.")
            // ->action('Renew Now', url('/vendor/subscription'))
            ->line('Thank you for staying with us.');
    }
}
