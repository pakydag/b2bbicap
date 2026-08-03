<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bPriceList extends Model
{
    protected $fillable = ['agent_id', 'name', 'description', 'general_discount_percent', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
        'general_discount_percent' => 'float',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function items()
    {
        return $this->hasMany(B2bPriceListItem::class, 'b2b_price_list_id');
    }

    public function customers()
    {
        return $this->hasMany(B2bCustomer::class, 'b2b_price_list_id');
    }
}
