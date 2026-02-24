<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ClaimAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $claimUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Customer $customer,
        public Order $order
    ) {
        // Generate a signed URL that expires in 7 days
        $this->claimUrl = URL::temporarySignedRoute(
            'account.claim.show',
            now()->addDays(7),
            ['customer' => $customer->id]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Account Setup - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.claim-account',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
