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
    public $groupOrders;
    public $allItems;
    public $totalAmount;
    public $paymentLink;
    public $paymentMethod;
    public $title;
    public $companyName;
    public $portalUrl;
    public $isEn;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $paymentLink = null, $paymentMethod = 'none', $title = null, $groupOrders = null)
    {
        $this->order = $order;
        $this->order->loadMissing('agent', 'customer.agents', 'customer.user', 'items.product.brand', 'items.variant');

        if ($groupOrders instanceof \Illuminate\Support\Collection) {
            $this->groupOrders = $groupOrders;
        } elseif (is_array($groupOrders)) {
            $this->groupOrders = collect($groupOrders);
        } elseif (!empty($order->order_group_id)) {
            $this->groupOrders = $order->groupOrders();
        } else {
            $this->groupOrders = collect([$order]);
        }

        foreach ($this->groupOrders as $gO) {
            $gO->loadMissing('agent', 'customer.agents', 'customer.user', 'items.product.brand', 'items.variant');
        }

        $this->allItems = $this->groupOrders->flatMap(fn($o) => $o->items);
        $this->totalAmount = $this->groupOrders->sum('total_amount');

        $this->paymentLink = $paymentLink;
        $this->paymentMethod = $paymentMethod;
        $this->isEn = ($order->customer?->user?->locale ?? $order->customer?->locale ?? app()->getLocale()) === 'en';
        
        $orderCount = $this->groupOrders->count();
        $orderIdsStr = $orderCount > 1 
            ? '#' . implode(', #', $this->groupOrders->pluck('id')->toArray())
            : "#{$order->id}";

        if ($title) {
            $this->title = $title;
        } else {
            $this->title = $orderCount > 1
                ? ($this->isEn ? "B2B Orders Confirmation {$orderIdsStr}" : "Riepilogo Ordini B2B {$orderIdsStr}")
                : ($this->isEn ? "B2B Order Confirmation {$orderIdsStr}" : "Riepilogo Ordine B2B {$orderIdsStr}");
        }

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
        $orderCount = $this->groupOrders ? $this->groupOrders->count() : 1;
        $orderIdsStr = $orderCount > 1 
            ? '#' . implode(', #', $this->groupOrders->pluck('id')->toArray())
            : "#{$this->order->id}";

        $prefix = $this->title ?? ($this->isEn ? 'B2B Order Confirmation' : 'Riepilogo Ordine B2B');
        $subject = str_contains($prefix, '#') ? $prefix : "{$prefix} {$orderIdsStr}";
        
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
