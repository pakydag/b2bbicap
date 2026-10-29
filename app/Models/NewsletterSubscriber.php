<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'locale',
        'coupon_code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}

