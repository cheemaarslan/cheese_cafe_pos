<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use App\Libraries\InventoryUnitLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryPurchaseResource extends JsonResource
{
    public function toArray($request): array
    {
        // Stored in the item's base unit (g, ml, pcs); shown in the unit it was bought in (kg, litre...).
        $purchaseUnit = optional($this->inventoryItem)->purchase_unit;
        $unitCost = InventoryUnitLibrary::costFromBase($this->unit_cost, $purchaseUnit);

        return [
            'id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'item_name' => optional($this->inventoryItem)->name ?? '',
            'unit' => $purchaseUnit ? InventoryUnitLibrary::label($purchaseUnit) : '',
            'quantity' => InventoryUnitLibrary::fromBase($this->quantity, $purchaseUnit),
            'unit_cost' => $unitCost,
            'unit_cost_currency_price' => AppLibrary::currencyAmountFormat($unitCost),
            'total_cost' => $this->total_cost,
            'total_cost_currency_price' => AppLibrary::currencyAmountFormat($this->total_cost),
            'purchase_date' => optional($this->purchase_date)->format('Y-m-d'),
            'converted_purchase_date' => $this->purchase_date ? AppLibrary::date($this->purchase_date) : '',
        ];
    }
}
