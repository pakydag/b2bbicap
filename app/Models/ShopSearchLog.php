<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSearchLog extends Model
{
    protected $fillable = [
        'query',
        'type',
        'results_count',
        'hits',
        'ip_address',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
