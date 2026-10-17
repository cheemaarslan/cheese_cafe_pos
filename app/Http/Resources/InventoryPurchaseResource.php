<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryPurchaseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'inventory_item_id' => $this->inventory_item_id,
            'item_name' => optional($this->inventoryItem)->name ?? '',
            'unit' => optional($this->inventoryItem)->unit ?? '',
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'unit_cost_currency_price' => AppLibrary::currencyAmountFormat($this->unit_cost),
            'total_cost' => $this->total_cost,
            'total_cost_currency_price' => AppLibrary::currencyAmountFormat($this->total_cost),
            'purchase_date' => optional($this->purchase_date)->format('Y-m-d'),
            'converted_purchase_date' => $this->purchase_date ? AppLibrary::date($this->purchase_date) : '',
        ];
    }
}
