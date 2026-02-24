<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeSequenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public int $step,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            1 => "Welcome! Here's 10% off your first order",
            2 => 'Our Story: What Makes Us Different',
            3 => '5 Reasons to Love Your New Favorite Store',
        ];

        return new Envelope(
            subject: $subjects[$this->step] ?? 'Welcome!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "emails.welcome-step{$this->step}",
        );
    }
}
