<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryRejectedMail extends Mailable
{
    use SerializesModels;

    public $inquiry;
    public $reason;

    public function __construct($inquiry, $reason)
    {
        $this->inquiry = $inquiry;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Inquiry Rejected Notification')
                    ->view('emails.inquiry_rejected');
    }
}
