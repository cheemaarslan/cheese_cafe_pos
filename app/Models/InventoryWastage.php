<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Stock thrown away: quantity is in the inventory item's base unit (g, ml or pcs), unit_cost per base unit at
 * the time. Written and removed only through InventoryWastageService, which moves the stock with it.
 */
class InventoryWastage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'quantity',
        'unit_cost',
        'reason',
        'note',
        'created_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'inventory_item_id' => 'integer',
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:6',
        'reason' => 'integer',
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
