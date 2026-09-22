<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class B2bOrder extends Model
{
    protected $fillable = ['agent_id', 'b2b_customer_id', 'internal_reference', 'contact_id', 'total_amount', 'status', 'payment_method', 'notes', 'is_modified'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function customer()
    {
        return $this->belongsTo(B2bCustomer::class, 'b2b_customer_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function items()
    {
        return $this->hasMany(B2bOrderItem::class, 'b2b_order_id');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'In Attesa',
            'revision_pending' => 'Attesa Cliente',
            'customer_approved' => 'Approvato da Cliente',
            'customer_rejected' => 'Rifiutato da Cliente',
            'confirmed' => 'Confermato',
            'cancelled' => 'Annullato',
            default => strtoupper($this->status),
        };
    }
}
