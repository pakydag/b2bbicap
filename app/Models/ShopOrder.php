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
        'spedizione_provincia', 'spedizione_nazione'
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
}
