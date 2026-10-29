<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $coupon;
    public $locale;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct($subscriber, $coupon = null, $locale = null)
    {
        $this->subscriber = $subscriber;
        $this->coupon = $coupon ?? \App\Models\ShopCoupon::where('code', 'welcomebicapshop')->first();

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
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isEn
            ? 'Welcome to BICAP - Here is your 5% discount coupon!'
            : 'Benvenuto in BICAP - Ecco il tuo coupon sconto del 5%!';

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
            view: 'emails.newsletter_welcome',
            with: [
                'subscriber' => $this->subscriber,
                'coupon' => $this->coupon,
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

