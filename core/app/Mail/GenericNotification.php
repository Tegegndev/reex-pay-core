<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;

    public string $message;

    public function __construct(string $title, string $message)
    {
        $this->title   = $title;
        $this->message = $message;

    }

    public function build(): self
    {
        return $this->subject($this->title)
            ->view('emails.generic_notification')
            ->with([
                'title' => $this->title,
                'body'  => $this->message,
            ]);
    }
}
