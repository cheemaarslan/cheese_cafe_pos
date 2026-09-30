<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerLoyaltyPoint extends Model
{
    use HasFactory;

    protected $table = 'customer_loyalty_points';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'customer_id',
        'order_id',
        'order_subtotal',
        'percentage',
        'points',
        'status',
        'earned_at',
        'expired_at',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'order_subtotal' => 'decimal:2',
        'percentage'     => 'decimal:2',
        'points'         => 'integer',
        'status'         => 'integer',
        'earned_at'      => 'datetime',
        'expired_at'     => 'datetime',
    ];

    /**
     * Relationships
     */

    // Customer (User)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
