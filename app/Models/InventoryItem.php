<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'quantity',
        'unit',
        'minimum_stock',
        'unit_cost',
        'supplier',
        'notes',
        'status',
    ];

    protected $casts = [
        'id' => 'integer',
        'quantity' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'unit_cost' => 'decimal:6',
        'status' => 'integer',
    ];
}
