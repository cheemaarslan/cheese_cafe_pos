<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemIngredientResource extends JsonResource
{
    public function toArray($request): array
    {
        $inventoryItem = $this->inventoryItem;
        $unitCost = optional($inventoryItem)->unit_cost;

        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_variation_id' => $this->item_variation_id,
            'item_extra_id' => $this->item_extra_id,
            'inventory_item_id' => $this->inventory_item_id,
            'inventory_item_name' => optional($inventoryItem)->name ?? '',
            'inventory_item_deleted' => $inventoryItem ? $inventoryItem->trashed() : true,
            // Base unit of the inventory item (g, ml or pcs), which quantity is in
            'unit' => optional($inventoryItem)->unit ?? '',
            'quantity' => $this->quantity,
            // Cost of the quantity at the inventory item's current unit cost; null when it has no cost set
            'cost' => $unitCost === null ? null : round((float) $this->quantity * (float) $unitCost, 6),
        ];
    }
}
