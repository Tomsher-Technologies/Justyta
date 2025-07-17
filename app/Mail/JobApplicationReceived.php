<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\JobPost;
use App\Models\JobApplication;

class JobApplicationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $jobPost;
    public $application;

    public function __construct(JobPost $jobPost, JobApplication $application)
    {
        $this->jobPost = $jobPost;
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('New Job Application Received')
                    ->view('emails.job-application-received');
    }
}
