<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $serverHost;
    public $fromEmail;
    public $fromName;
    public $sentAt;

    public function __construct(string $type, string $serverHost, string $fromEmail, string $fromName)
    {
        $this->type = $type;
        $this->serverHost = $serverHost;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->sentAt = now()->format('d/m/Y H:i:s');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'BICAP - Test Connessione Email SMTP (' . $this->type . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test_email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
