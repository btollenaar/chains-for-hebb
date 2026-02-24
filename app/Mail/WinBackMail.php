<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WinBackMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public int $step,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            1 => "We miss you! Here's what's new",
            2 => 'Last chance: 10% off to welcome you back',
        ];

        return new Envelope(
            subject: $subjects[$this->step] ?? 'We miss you!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "emails.win-back-step{$this->step}",
        );
    }
}
