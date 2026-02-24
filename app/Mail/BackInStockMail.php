<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BackInStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Product $product) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->product->name} is Back in Stock!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.back-in-stock',
        );
    }
}
