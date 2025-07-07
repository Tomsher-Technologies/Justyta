<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $serviceRequest;
    public $forAdmin;

    public function __construct($serviceRequest, $forAdmin = false)
    {
        $this->serviceRequest = $serviceRequest;
        $this->forAdmin = $forAdmin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $serviceName = $this->serviceRequest->service->name ?? 'Service';
        if ($this->forAdmin) {
            return (new MailMessage)
                ->subject('New '.$serviceName.' Request Submitted')
                ->line('A new '.$serviceName.' request has been submitted.')
                ->line('Reference Code: ' . $this->serviceRequest->reference_code);
        }

        return (new MailMessage)
            ->subject('Your '.$serviceName.' Request has been Submitted')
            ->line('Thank you for submitting your request.')
            ->line('Reference Code: ' . $this->serviceRequest->reference_code);
    }

    public function toDatabase($notifiable)
    {
        $serviceName = $this->serviceRequest->service->name ?? 'Service';

        return [
            'service' => $this->serviceRequest->service->slug,
            'reference_code' => $this->serviceRequest->reference_code,
            'message' => $this->forAdmin
                ? "New $serviceName request submitted (Ref: {$this->serviceRequest->reference_code})"
                : 'messages.service_request_submitted',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
