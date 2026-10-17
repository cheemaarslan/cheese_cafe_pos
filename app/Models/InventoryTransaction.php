<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One stock movement of an inventory item, in its base unit: positive is stock in, negative is stock out.
 * Written only through InventoryStockService, so an item's quantity always equals the sum of its rows.
 */
class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity',
        'balance_after',
        'unit_cost',
        'order_id',
        'note',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'inventory_item_id' => 'integer',
        'type' => 'integer',
        'quantity' => 'decimal:3',
        'balance_after' => 'decimal:3',
        'unit_cost' => 'decimal:6',
        'order_id' => 'integer',
        'created_by' => 'integer',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class)->withTrashed();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
