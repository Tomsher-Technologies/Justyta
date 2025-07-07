<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ServiceRequestStatusChanged extends Notification
{
    use Queueable;

    public $serviceRequest;

    public function __construct($serviceRequest)
    {
        $this->serviceRequest = $serviceRequest;
    }

    public function via($notifiable)
    {
        return ['database']; // or just ['database'] if no email , 'mail'
    }

    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject('Service Request Status Updated')
    //         ->line("Your service request (Ref: {$this->serviceRequest->reference_code}) has been updated to: " . ucfirst($this->serviceRequest->status))
    //         ->action('View Request', url('/service-requests/' . $this->serviceRequest->id));
    // }

    public function toArray($notifiable)
    {
        return [
            'service' => $this->serviceRequest->service->slug,
            'reference_code' => $this->serviceRequest->reference_code,
            'status' => $this->serviceRequest->status,
            'message' => 'messages.service_request_status_change',
        ];
    }
}
