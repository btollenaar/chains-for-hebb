<?php

namespace App\Mail;

use App\Models\DataExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DataExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DataExport $dataExport) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Data Export is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.data-export-ready',
        );
    }
}
