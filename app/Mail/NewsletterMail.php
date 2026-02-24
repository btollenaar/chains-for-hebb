<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Newsletter $newsletter,
        public ?NewsletterSend $send = null,
        public bool $isTest = false
    ) {
        $this->newsletter->load('lists');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isTest
            ? '[TEST] ' . $this->newsletter->subject
            : $this->newsletter->subject;

        $fromName = $this->newsletter->from_name ?? config('mail.from.name');
        $fromEmail = $this->newsletter->from_email ?? config('mail.from.address');

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.newsletter',
            text: 'emails.newsletter-plain',
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

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->envelope()
            ->content();

        // Add compliance headers
        $this->withSymfonyMessage(function ($message) {
            $headers = $message->getHeaders();

            // Bulk email precedence
            $headers->addTextHeader('Precedence', 'bulk');

            // Auto-response suppression
            $headers->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

            // List-Unsubscribe header (one-click unsubscribe)
            if ($this->send) {
                $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->send->tracking_token]);
                $headers->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            }
        });

        return $this;
    }
}
