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
        'discount_value'
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
}
