<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReturnRequest $returnRequest,
    ) {
        $this->returnRequest->load(['order', 'customer']);
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Return Request Approved - #' . $this->returnRequest->return_number,
            'rejected' => 'Return Request Update - #' . $this->returnRequest->return_number,
            'completed' => 'Refund Processed - #' . $this->returnRequest->return_number,
        ];

        return new Envelope(
            subject: $subjects[$this->returnRequest->status] ?? 'Return Request Update - #' . $this->returnRequest->return_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.return-status',
        );
    }
}
