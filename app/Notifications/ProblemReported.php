<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProblemReported extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Mail + system notification
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Problem Reported')
            ->line('A new problem has been reported by a user.')
            ->line('Subject: ' . $this->report->subject)
            ->line('Email: ' . $this->report->email)
            ->line('Message:')
            ->line($this->report->message)
            // ->action('View Reports', url('/admin/reports')) // customize as needed
            ->line('Thank you.');
    }

    public function toArray($notifiable)
    {
        return [
            'service' => NULL,
            'reference_code' => NULL,
            'message' => 'New problem reported',
        ];
    }
}