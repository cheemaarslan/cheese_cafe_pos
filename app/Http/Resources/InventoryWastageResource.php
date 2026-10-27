<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use App\Libraries\InventoryUnitLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryWastageResource extends JsonResource
{
    public function toArray($request): array
    {
        $inventoryItem = $this->inventoryItem;
        $purchaseUnit = optional($inventoryItem)->purchase_unit;
        $value = $this->unit_cost === null ? null : round((float) $this->quantity * (float) $this->unit_cost, 6);

        return [
            'id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'item_name' => optional($inventoryItem)->name ?? '',
            // Base unit (g, ml, pcs), and the same in the item's purchase unit (kg, litre...)
            'quantity' => $this->quantity,
            'unit' => optional($inventoryItem)->unit ?? '',
            'purchase_quantity' => InventoryUnitLibrary::fromBase($this->quantity, $purchaseUnit),
            'purchase_unit_label' => InventoryUnitLibrary::label($purchaseUnit),
            'reason' => $this->reason,
            'note' => $this->note ?? '',
            'value' => $value,
            'value_currency_price' => $value === null ? '' : AppLibrary::currencyAmountFormat($value),
            'created_by_name' => optional($this->creator)->name ?? '',
            'converted_created_at' => $this->created_at ? AppLibrary::datetime($this->created_at) : '',
        ];
    }
}
