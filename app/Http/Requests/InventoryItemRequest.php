<?php

namespace App\Http\Requests;

use App\Enums\Status;
use App\Libraries\InventoryUnitLibrary;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $inventoryItem = $this->route('inventoryItem');
        $inventoryItemId = optional($inventoryItem)->id;

        // A new item, or one switched to another kind of unit (e.g. pcs to litre), gets its stock as a total
        // in `quantity`. Otherwise stock bought (or removed) is `add_quantity`, added to the current stock.
        $stockIsTotal = ! $inventoryItem
            || InventoryUnitLibrary::baseUnit($this->input('purchase_unit')) !== $inventoryItem->unit;

        return [
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique('inventory_items', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($inventoryItemId),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('inventory_items', 'sku')
                    ->whereNull('deleted_at')
                    ->ignore($inventoryItemId),
            ],
            'category' => ['nullable', 'string', 'max:190'],
            // Amounts are per purchase_unit; they're stored in its base unit (up to 1000x larger), hence the
            // lower maximums.
            'purchase_unit' => ['required', 'string', Rule::in(InventoryUnitLibrary::units())],
            'quantity' => $stockIsTotal ? ['required', 'numeric', 'min:0', 'max:9999999999999.999'] : ['nullable'],
            'add_quantity' => ['nullable', 'numeric', 'min:-9999999999999.999', 'max:9999999999999.999'],
            'minimum_stock' => ['required', 'numeric', 'min:0', 'max:9999999999999.999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.999999'],
            'supplier' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'integer', Rule::in([Status::ACTIVE, Status::INACTIVE])],
        ];
    }
}
