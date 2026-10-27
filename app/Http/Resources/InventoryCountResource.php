<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use App\Libraries\InventoryUnitLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryCountResource extends JsonResource
{
    public function toArray($request): array
    {
        $items = $this->relationLoaded('items') ? $this->items : null;
        $differenceValue = $items === null
            ? (float) $this->difference_value
            : $items->sum(fn ($item) => (float) $item->difference * (float) $item->unit_cost);

        return [
            'id' => $this->id,
            'note' => $this->note ?? '',
            'items_count' => $items === null ? (int) $this->items_count : $items->count(),
            'changed_items_count' => $items === null
                ? (int) $this->changed_items_count
                : $items->filter(fn ($item) => (float) $item->difference != 0)->count(),
            // Value of the stock found minus what the system had: negative is stock missing
            'difference_value' => round($differenceValue, 6),
            'difference_value_currency_price' => AppLibrary::currencyAmountFormat($differenceValue),
            'created_by_name' => optional($this->creator)->name ?? '',
            'converted_created_at' => $this->created_at ? AppLibrary::datetime($this->created_at) : '',
            'items' => $items === null ? [] : $items->map(function ($item) {
                $inventoryItem = $item->inventoryItem;
                $unit = optional($inventoryItem)->purchase_unit;
                $value = $item->unit_cost === null ? null : round((float) $item->difference * (float) $item->unit_cost, 6);

                return [
                    'id' => $item->id,
                    'inventory_item_id' => $item->inventory_item_id,
                    'item_name' => optional($inventoryItem)->name ?? '',
                    'unit' => InventoryUnitLibrary::label($unit),
                    // In the item's purchase unit (kg, litre...)
                    'system_quantity' => InventoryUnitLibrary::fromBase($item->system_quantity, $unit),
                    'counted_quantity' => InventoryUnitLibrary::fromBase($item->counted_quantity, $unit),
                    'difference' => InventoryUnitLibrary::fromBase($item->difference, $unit),
                    'value' => $value,
                    'value_currency_price' => $value === null ? '' : AppLibrary::currencyAmountFormat($value),
                ];
            })->values(),
        ];
    }
}
