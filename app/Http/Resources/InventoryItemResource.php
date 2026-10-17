<?php

namespace App\Http\Resources;

use App\Libraries\InventoryUnitLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku ?? '',
            'category' => $this->category ?? '',
            // Base unit (g, ml, pcs): what stock is kept and deducted in
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'minimum_stock' => $this->minimum_stock,
            'unit_cost' => $this->unit_cost,
            // The same amounts in the purchase unit (kg, litre...), as the admin enters and reads them
            'purchase_unit' => $this->purchase_unit,
            'purchase_unit_label' => InventoryUnitLibrary::label($this->purchase_unit),
            'purchase_quantity' => InventoryUnitLibrary::fromBase($this->quantity, $this->purchase_unit),
            'purchase_minimum_stock' => InventoryUnitLibrary::fromBase($this->minimum_stock, $this->purchase_unit),
            'purchase_unit_cost' => $this->unit_cost === null
                ? null
                : InventoryUnitLibrary::costFromBase($this->unit_cost, $this->purchase_unit),
            'total_value' => $this->unit_cost === null
                ? null
                : number_format((float) $this->quantity * (float) $this->unit_cost, 6, '.', ''),
            'supplier' => $this->supplier ?? '',
            'notes' => $this->notes ?? '',
            'status' => $this->status,
            'is_low_stock' => (float) $this->quantity <= (float) $this->minimum_stock,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
