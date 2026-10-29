<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $locale;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $locale = null)
    {
        $this->order = $order;
        $this->order->loadMissing('customer', 'items.variant.product');

        if ($locale) {
            $this->locale = $locale;
        } elseif (session()->has('locale')) {
            $this->locale = session('locale');
        } elseif (!empty($order->customer?->locale)) {
            $this->locale = $order->customer->locale;
        } elseif (!empty($order->customer?->user?->locale)) {
            $this->locale = $order->customer->user->locale;
        } elseif (
            !empty($order->spedizione_nazione) &&
            !in_array(strtolower(trim($order->spedizione_nazione)), ['italia', 'italy', 'it'])
        ) {
            $this->locale = 'en';
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
            ? 'Order Confirmation #' . $this->order->numero_ordine
            : 'Conferma Ordine #' . $this->order->numero_ordine;

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
            view: 'emails.order_confirmed',
            with: [
                'order' => $this->order,
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
