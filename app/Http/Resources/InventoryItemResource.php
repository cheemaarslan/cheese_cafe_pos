<?php

namespace App\Http\Resources;

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
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'minimum_stock' => $this->minimum_stock,
            'unit_cost' => $this->unit_cost,
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
