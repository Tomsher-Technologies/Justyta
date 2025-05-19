<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class VendorDocumentExpiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $documentName;
    protected $expiryDate;

    public function __construct($documentName, Carbon $expiryDate)
    {
        $this->documentName = $documentName;
        $this->expiryDate = $expiryDate->format('d M Y');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Document Expiry Reminder: ' . $this->documentName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your document **{$this->documentName}** is set to expire on **{$this->expiryDate}**.")
            ->line('Please update your documents to avoid any disruptions.')
            ->line('Thank you for staying compliant.');
    }
}
