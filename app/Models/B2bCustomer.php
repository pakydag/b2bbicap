<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bCustomer extends Model
{
    protected $fillable = [
        'code', 'business_name', 'vat_number', 'contact_name', 
        'contact_surname', 'phone', 'email', 'payment_condition_id',
        'b2b_price_list_id'
    ];

    public function paymentCondition()
    {
        return $this->belongsTo(B2bPaymentCondition::class, 'payment_condition_id');
    }

    public function priceList()
    {
        return $this->belongsTo(B2bPriceList::class, 'b2b_price_list_id');
    }

    public function agents()
    {
        return $this->belongsToMany(User::class, 'agent_customer', 'b2b_customer_id', 'user_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'b2b_customer_id');
    }

    public function orders()
    {
        return $this->hasMany(B2bOrder::class, 'b2b_customer_id');
    }
}
