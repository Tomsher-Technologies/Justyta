<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class CommonMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $array;

    public function __construct($array)
    {
        $this->array = $array;
    }

     public function build()
    {
        return $this->view('emails.commonmail')
                    ->from($this->array['from'], env('MAIL_FROM_NAME'))
                    ->subject($this->array['subject'])
                    ->with([
                        'content' => $this->array['content'],
                        'subject' => $this->array['subject'],
                    ]);
    }
}
