<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OTPUpdateUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp, $type)
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your OTP Code edit ' .  $this->type)
                    ->view('emails.otp-update-user');
    }
}
