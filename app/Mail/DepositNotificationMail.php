<?php

namespace App\Mail;

use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $deposit;

    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject:  config('app.name') . ' - Thông báo yêu cầu nạp tiền mới - ' . number_format($this->deposit->amount) . ' VNĐ',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deposit-notification',
            with: [
                'deposit' => $this->deposit,
                'user' => $this->deposit->user,
                'bank' => $this->deposit->bank,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}