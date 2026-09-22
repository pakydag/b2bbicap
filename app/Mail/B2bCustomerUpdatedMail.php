<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class B2bCustomerUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $loginEmail;
    public $companyName;
    public $portalUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($customer, $loginEmail = null)
    {
        $this->customer = $customer;
        $this->customer->loadMissing('agents', 'paymentCondition', 'priceList');
        $this->loginEmail = $loginEmail ?: ($customer->user?->email ?? $customer->email);
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
        return new Envelope(
            subject: 'Aggiornamento Dati e Profilo B2B - ' . $this->companyName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_updated',
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
