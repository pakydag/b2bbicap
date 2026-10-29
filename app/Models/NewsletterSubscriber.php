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
        'verification_token',
        'confirmed',
        'confirmed_at',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];
}

