<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostPurchaseFollowUpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {
        $this->order->load(['items.item', 'customer']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How are you enjoying your order? We\'d love to hear from you!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.post-purchase-follow-up',
        );
    }
}
