<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The only place an inventory item's stock quantity changes. Each change is written to the stock
 * ledger (inventory_transactions) in the same database transaction, so an item's quantity always
 * equals the sum of its ledger rows and every gram can be traced to a purchase, order or adjustment.
 */
class InventoryStockService
{
    /**
     * Move stock in (+) or out (-) by $quantity in the item's base unit (g, ml or pcs). Stock may go
     * below zero: a sale is never blocked because the system's count ran out.
     *
     * @param  array  $attributes  optional order_id, note, unit_cost (defaults to the item's unit cost)
     */
    public function move(InventoryItem $inventoryItem, float $quantity, int $type, array $attributes = []): ?InventoryTransaction
    {
        $quantity = round($quantity, 3);
        if ($quantity == 0) {
            return null;
        }

        return DB::transaction(function () use ($inventoryItem, $quantity, $type, $attributes) {
            // Locked so concurrent orders can't both read the same balance and lose one deduction.
            $item = InventoryItem::withTrashed()->lockForUpdate()->findOrFail($inventoryItem->id);
            $balance = round((float) $item->quantity + $quantity, 3);

            $item->quantity = $balance;
            $item->save();
            $inventoryItem->quantity = $item->quantity;

            return InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type' => $type,
                'quantity' => $quantity,
                'balance_after' => $balance,
                'unit_cost' => array_key_exists('unit_cost', $attributes) ? $attributes['unit_cost'] : $item->unit_cost,
                'order_id' => $attributes['order_id'] ?? null,
                'note' => $attributes['note'] ?? null,
                'created_by' => Auth::id(),
            ]);
        });
    }
}
