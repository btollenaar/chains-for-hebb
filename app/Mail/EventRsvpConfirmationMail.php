<?php

namespace App\Mail;

use App\Models\EventRsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventRsvpConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EventRsvp $rsvp
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->rsvp->status === 'waitlisted' ? 'Waitlisted' : 'Confirmed';
        return new Envelope(
            subject: "RSVP {$status}: {$this->rsvp->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-rsvp-confirmation',
        );
    }
}
