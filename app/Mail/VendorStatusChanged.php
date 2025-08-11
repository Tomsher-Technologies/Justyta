<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $vendorName;
    public $status;

    public function __construct($vendorName, $status)
    {
        $this->vendorName = $vendorName;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject("Your Law Firm Account Has Been " . ucfirst($this->status))
                    ->markdown('emails.vendor.status');
    }
}

