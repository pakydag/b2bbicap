<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bPriceListItem extends Model
{
    protected $fillable = [
        'b2b_price_list_id',
        'b2b_product_id',
        'min_quantity',
        'max_quantity',
        'discount_type',
        'discount_value',
        'discount_2',
        'discount_3'
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'discount_value' => 'float',
    ];

    public function priceList()
    {
        return $this->belongsTo(B2bPriceList::class, 'b2b_price_list_id');
    }

    public function product()
    {
        return $this->belongsTo(B2bProduct::class, 'b2b_product_id');
    }

    /**
     * Restituisce la descrizione formattata della regola di sconto/prezzo.
     */
    public function getRuleSummaryAttribute(): string
    {
        $extra = [];
        if ($this->discount_2 > 0) {
            $extra[] = ($this->discount_type === 'percentage' ? '+' : '-') . floatval($this->discount_2) . '%';
        }
        if ($this->discount_3 > 0) {
            $extra[] = ($this->discount_type === 'percentage' ? '+' : '-') . floatval($this->discount_3) . '%';
        }
        $extraStr = !empty($extra) ? ' ' . implode(' ', $extra) : '';

        if ($this->discount_type === 'fixed_price') {
            return 'Prezzo Netto: € ' . number_format($this->discount_value, 2, ',', '.') . $extraStr;
        } elseif ($this->discount_type === 'percentage') {
            return 'Sconto: -' . floatval($this->discount_value) . '%' . $extraStr;
        } elseif ($this->discount_type === 'discount_amount') {
            return 'Sconto: -€ ' . number_format($this->discount_value, 2, ',', '.') . $extraStr;
        }

        return '';
    }

    /**
     * Calcola il prezzo unitario applicando la regola e gli sconti a cascata su un dato prezzo base.
     */
    public function calculateUnitPrice(float $basePrice): float
    {
        if ($this->discount_type === 'percentage') {
            $unitPrice = $basePrice * (1 - ($this->discount_value / 100));
            if ($this->discount_2 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_2 / 100));
            }
            if ($this->discount_3 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_3 / 100));
            }
            return max(0, round($unitPrice, 2));
        } elseif ($this->discount_type === 'fixed_price') {
            $unitPrice = (float) $this->discount_value;
            if ($this->discount_2 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_2 / 100));
            }
            if ($this->discount_3 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_3 / 100));
            }
            return max(0, round($unitPrice, 2));
        } elseif ($this->discount_type === 'discount_amount') {
            $unitPrice = $basePrice - (float) $this->discount_value;
            if ($this->discount_2 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_2 / 100));
            }
            if ($this->discount_3 > 0) {
                $unitPrice = $unitPrice * (1 - ($this->discount_3 / 100));
            }
            return max(0, round($unitPrice, 2));
        }

        return $basePrice;
    }
}

