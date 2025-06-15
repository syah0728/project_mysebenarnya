<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Progress;

class InquiryProgressMail extends Mailable
{
    use Queueable, SerializesModels;

    public $progress;

    public function __construct(Progress $progress)
    {
        $this->progress = $progress;
    }

    public function build()
    {
        return $this->subject('Your Inquiry Has Been Updated')
                    ->view('emails.inquiry_progress');
    }
}
