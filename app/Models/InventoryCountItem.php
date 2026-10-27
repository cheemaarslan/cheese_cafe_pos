<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One item of a stock count, in its base unit (g, ml or pcs): what the system had, what was counted, and the
 * difference (counted - system) that was booked.
 */
class InventoryCountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_count_id',
        'inventory_item_id',
        'system_quantity',
        'counted_quantity',
        'difference',
        'unit_cost',
    ];

    protected $casts = [
        'id' => 'integer',
        'inventory_count_id' => 'integer',
        'inventory_item_id' => 'integer',
        'system_quantity' => 'decimal:3',
        'counted_quantity' => 'decimal:3',
        'difference' => 'decimal:3',
        'unit_cost' => 'decimal:6',
    ];

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class)->withTrashed();
    }
}
