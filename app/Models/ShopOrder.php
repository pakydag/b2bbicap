<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrder extends Model
{
    protected $fillable = [
        'customer_id', 'numero_ordine', 'stato', 'stato_pagamento', 'metodo_pagamento',
        'note_cliente', 'note_admin', 'coupon_codice', 'totale_sconto',
        'totale_imponibile', 'totale_iva', 'totale_ordine',
        'costo_spedizione', 'costo_contrassegno',
        'spedizione_nome', 'spedizione_indirizzo', 'spedizione_cap', 'spedizione_citta',
        'spedizione_provincia', 'spedizione_nazione',
        'corriere', 'tracking_number', 'tracking_url', 'spedito_il'
    ];

    protected $casts = [
        'spedito_il' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function items()
    {
        return $this->hasMany(ShopOrderItem::class);
    }

    /**
     * Determina se la spedizione è diretta in Italia o all'estero
     */
    public function isItalianShipping(): bool
    {
        $n = mb_strtolower(trim($this->spedizione_nazione ?? ''));
        return in_array($n, ['it', 'italia', 'italy', '']) || empty($n);
    }

    /**
     * Costruisce o restituisce il link diretto di tracciamento
     */
    public function getEffectiveTrackingUrlAttribute(): ?string
    {
        if (!empty($this->tracking_url)) {
            return $this->tracking_url;
        }

        if (empty($this->tracking_number)) {
            return null;
        }

        $code = trim($this->tracking_number);
        $corriere = mb_strtolower(trim($this->corriere ?? ''));

        if (str_contains($corriere, 'brt') || str_contains($corriere, 'bartolini')) {
            return "https://www.brt.it/it/tracking?codiceSpedizione={$code}";
        }
        if (str_contains($corriere, 'gls')) {
            return "https://gls-group.com/IT/it/servizi-online/ricerca-spedizioni.html?match={$code}";
        }
        if (str_contains($corriere, 'dhl')) {
            return "https://www.dhl.com/it-it/home/tracciamento.html?tracking-id={$code}";
        }
        if (str_contains($corriere, 'poste') || str_contains($corriere, 'sda')) {
            return "https://www.poste.it/cerca/index.html#/risultati-spedizioni/{$code}";
        }
        if (str_contains($corriere, 'ups')) {
            return "https://www.ups.com/track?tracknum={$code}";
        }
        if (str_contains($corriere, 'fedex') || str_contains($corriere, 'tnt')) {
            return "https://www.fedex.com/fedextrack/?trknbr={$code}";
        }

        return null;
    }
}
