<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bCart extends Model
{
    protected $fillable = ['user_id', 'cart_data'];

    protected $casts = [
        'cart_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
