<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbandonedCartSequenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public Collection $cartItems,
        public float $cartTotal,
        public int $step,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            1 => 'You left something behind!',
            2 => "Still thinking about it? Here's why our customers love these",
            3 => 'Last chance: 5% off your cart',
        ];

        return new Envelope(
            subject: $subjects[$this->step] ?? 'Your cart is waiting',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: "emails.abandoned-cart-step{$this->step}",
        );
    }
}
