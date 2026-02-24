<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $newStatus,
    ) {
        $this->order->load(['items.item', 'customer']);
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'shipped' => 'Your Order Has Shipped - #' . $this->order->order_number,
            'delivered' => 'Your Order Has Been Delivered - #' . $this->order->order_number,
        ];

        return new Envelope(
            subject: $subjects[$this->newStatus] ?? 'Order Update - #' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-status-update',
        );
    }
}
