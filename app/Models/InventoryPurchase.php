<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One stock purchase: written automatically whenever an inventory item is created with stock or its
 * quantity is increased. Its total_cost is what the Profit & Loss report counts as inventory expense.
 */
class InventoryPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'purchase_date',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'inventory_item_id' => 'integer',
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:6',
        'total_cost' => 'decimal:6',
        'purchase_date' => 'date',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class)->withTrashed();
    }
}
