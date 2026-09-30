<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $table = "loyalty_points";
    protected $fillable = ['percentage', 'status'];
    protected $casts = [
        'id'         => 'integer',
        'percentage' => 'decimal:2',
        'status'     => 'integer',
    ];
}
