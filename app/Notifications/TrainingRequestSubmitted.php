<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\TrainingRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TrainingRequestSubmitted extends Notification 
{
    use Queueable;

    public $trainingRequest;
    public $forAdmin;

    public function __construct(TrainingRequest $trainingRequest, $forAdmin = false)
    {
        $this->trainingRequest = $trainingRequest;
        $this->forAdmin = $forAdmin;
    }

    public function via($notifiable)
    {
        return ['database']; // or ['mail', 'database'] if you also want email
    }

    public function toDatabase($notifiable)
    {
        return [
            'service' => null,
            'reference_code' => null,
            'message' => $this->forAdmin ? 'New training request received.' : 'messages.training_request_submitted',
        ];
    }
}