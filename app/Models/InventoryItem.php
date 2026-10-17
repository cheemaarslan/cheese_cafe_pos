<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * quantity, minimum_stock and unit_cost are in the base `unit` (g, ml or pcs); `purchase_unit` is the unit
 * the admin enters and sees them in (kg, litre, dozen...). See InventoryUnitLibrary.
 */
class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'category',
        'quantity',
        'unit',
        'purchase_unit',
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

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
