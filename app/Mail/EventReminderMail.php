<?php

namespace App\Mail;

use App\Models\EventRsvp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EventRsvp $rsvp
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: {$this->rsvp->event->title} is Tomorrow!",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event-reminder',
        );
    }
}
