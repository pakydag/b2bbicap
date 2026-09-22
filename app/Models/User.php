<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    
    protected static function booted()
    {
        static::saved(function ($user) {
            // Create or update the Contact in CRM
            Contact::updateOrCreate(
                ['email' => $user->email],
                [
                    'first_name' => $user->name,
                    'last_name' => $user->surname ?? '',
                    'phone' => $user->phone,
                    'is_active' => true,
                    // Map roles to contact types if needed
                ]
            );
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
        'role',
        'is_super_admin',
        'can_manage_site',
        'can_manage_shop',
        'can_manage_booking',
        'can_manage_voip',
        'can_manage_agents',
        'b2b_customer_id',
    ];

    /**
     * Get the brands that the agent is authorized to view.
     */
    public function b2bBrands()
    {
        return $this->belongsToMany(B2bBrand::class, 'agent_brand', 'user_id', 'b2b_brand_id');
    }

    /**
     * Get the customers that the agent is authorized to manage.
     */
    public function b2bCustomers()
    {
        return $this->belongsToMany(B2bCustomer::class, 'agent_customer', 'user_id', 'b2b_customer_id');
    }

    public function b2bOrders()
    {
        return $this->hasMany(B2bOrder::class, 'agent_id');
    }

    /**
     * Get the B2B customer profile associated with this user.
     */
    public function b2bCustomer()
    {
        return $this->belongsTo(B2bCustomer::class, 'b2b_customer_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
