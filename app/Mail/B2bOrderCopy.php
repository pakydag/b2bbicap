<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class B2bOrderCopy extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $paymentLink;
    public $paymentMethod;
    public $title;
    public $companyName;
    public $portalUrl;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $paymentLink = null, $paymentMethod = 'none', $title = null)
    {
        $this->order = $order;
        $this->order->loadMissing('agent', 'customer.agents', 'customer.user', 'items.product.brand', 'items.variant');
        $this->paymentLink = $paymentLink;
        $this->paymentMethod = $paymentMethod;
        $this->isEn = ($order->customer?->user?->locale ?? $order->customer?->locale ?? app()->getLocale()) === 'en';
        $this->title = $title ?: ($this->isEn ? 'B2B Order Confirmation' : 'Riepilogo Ordine B2B');
        $this->companyName = Setting::where('key', 'mail_from_name')->value('value') ?? config('mail.from.name') ?? 'Calzaturificio 5b';

        if (request() && request()->getHost() && request()->getHost() !== 'localhost') {
            $this->portalUrl = request()->getSchemeAndHttpHost() . request()->getBaseUrl() . '/login';
        } else {
            $this->portalUrl = route('login');
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $prefix = $this->title ?? ($this->isEn ? 'B2B Order Confirmation' : 'Riepilogo Ordine B2B');
        $subject = "{$prefix} #{$this->order->id}";
        if (!empty($this->order->internal_reference)) {
            $subject .= ($this->isEn ? " (Ref: " : " (Rif: ") . "{$this->order->internal_reference})";
        }
        $subject .= " - {$this->companyName}";

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
            view: 'emails.order_copy',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
