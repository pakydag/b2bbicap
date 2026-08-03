<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bOrderItem extends Model
{
    protected $fillable = ['b2b_order_id', 'b2b_product_id', 'b2b_product_variant_id', 'quantity', 'price', 'delivery_date', 'original_quantity', 'original_price', 'is_modified'];

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
}
