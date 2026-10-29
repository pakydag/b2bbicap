<?php

namespace App\Mail;

use App\Models\ShopOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $locale;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct(ShopOrder $order, ?string $locale = null)
    {
        $this->order = $order;
        $this->order->loadMissing('customer', 'items.variant.product');

        if ($locale) {
            $this->locale = $locale;
        } elseif (!$order->isItalianShipping()) {
            $this->locale = 'en';
        } else {
            $this->locale = 'it';
        }

        $this->isEn = ($this->locale === 'en');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isEn
            ? 'Your BICAP order #' . $this->order->numero_ordine . ' has been shipped!'
            : 'Il tuo ordine BICAP #' . $this->order->numero_ordine . ' è in viaggio!';

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
            view: 'emails.order_shipped',
            with: [
                'order' => $this->order,
                'isEn' => $this->isEn,
                'locale' => $this->locale,
                'trackingUrl' => $this->order->effective_tracking_url,
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
