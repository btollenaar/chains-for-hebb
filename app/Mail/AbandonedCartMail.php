<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AbandonedCartMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public Collection $cartItems,
        public float $cartTotal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You left something behind! Complete your order',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.abandoned-cart',
        );
    }
}
