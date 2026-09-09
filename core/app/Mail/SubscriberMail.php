<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $messageContent
    ) {}

    /**
     * Define envelope with dynamic subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText
        );
    }

    /**
     * Define view and pass data to it
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscriber',
            with: [
                'messageContent' => $this->messageContent,
                'subject'        => $this->subjectText,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
