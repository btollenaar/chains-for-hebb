<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {
        $this->order->load(['items.item', 'customer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How was your order? Leave a review!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.review-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
