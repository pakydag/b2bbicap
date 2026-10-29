<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $confirmationUrl;
    public $locale;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct($subscriber, $locale = null)
    {
        $this->subscriber = $subscriber;

        if ($locale) {
            $this->locale = $locale;
        } elseif (!empty($subscriber?->locale)) {
            $this->locale = $subscriber->locale;
        } elseif (session()->has('locale')) {
            $this->locale = session('locale');
        } else {
            $this->locale = app()->getLocale();
        }

        $this->isEn = ($this->locale === 'en');
        $this->confirmationUrl = route('public.newsletter.confirm', ['token' => $subscriber->verification_token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isEn
            ? 'Confirm your BICAP newsletter subscription & get your discount coupon'
            : 'Conferma la tua iscrizione alla newsletter BICAP e ricevi il coupon sconto';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter_confirm',
            with: [
                'subscriber' => $this->subscriber,
                'confirmationUrl' => $this->confirmationUrl,
                'isEn' => $this->isEn,
                'locale' => $this->locale,
            ],
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
