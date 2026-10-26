<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bOrderItem extends Model
{
    protected $fillable = [
        'b2b_order_id',
        'b2b_product_id',
        'b2b_product_variant_id',
        'product_name',
        'product_code',
        'brand_name',
        'variant_size',
        'variant_color',
        'base_price',
        'discount_rule_summary',
        'discount_type',
        'discount_value',
        'quantity',
        'price',
        'delivery_date',
        'original_quantity',
        'original_price',
        'is_modified',
    ];

    public function order()
    {
        return $this->belongsTo(B2bOrder::class, 'b2b_order_id');
    }

    public function product()
    {
        return $this->belongsTo(B2bProduct::class, 'b2b_product_id');
    }

    public function variant()
    {
        return $this->belongsTo(B2bProductVariant::class, 'b2b_product_variant_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->product_name ?: ($this->product?->name ?? 'Prodotto N.D.');
    }

    public function getDisplayCodeAttribute(): string
    {
        return $this->product_code ?: ($this->product?->code ?? '-');
    }

    public function getDisplayBrandNameAttribute(): ?string
    {
        return $this->brand_name ?: ($this->product?->brand?->name ?? null);
    }

    public function getDisplaySizeAttribute(): string
    {
        return $this->variant_size ?: ($this->variant?->size ?? '-');
    }

    public function getDisplayColorAttribute(): ?string
    {
        return $this->variant_color ?: ($this->variant?->color ?? null);
    }

    public function getDisplayBasePriceAttribute(): float
    {
        if ($this->base_price !== null) {
            return (float) $this->base_price;
        }
        return $this->product ? (float) $this->product->price : (float) $this->price;
    }
}
