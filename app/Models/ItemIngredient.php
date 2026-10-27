<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One line of a menu item's recipe: how much of an inventory item (in its base unit: g, ml or pcs) one of the
 * menu item uses. Without a variation or extra it's used by every order of the item; with one, only when that
 * variation is chosen or that extra added, in addition to the every-order lines.
 */
class ItemIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'item_variation_id',
        'item_extra_id',
        'inventory_item_id',
        'quantity',
    ];

    protected $casts = [
        'id' => 'integer',
        'item_id' => 'integer',
        'item_variation_id' => 'integer',
        'item_extra_id' => 'integer',
        'inventory_item_id' => 'integer',
        'quantity' => 'decimal:3',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function itemVariation(): BelongsTo
    {
        return $this->belongsTo(ItemVariation::class)->withTrashed();
    }

    public function itemExtra(): BelongsTo
    {
        return $this->belongsTo(ItemExtra::class)->withTrashed();
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class)->withTrashed();
    }
}
