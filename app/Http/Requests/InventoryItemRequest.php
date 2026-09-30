<?php

namespace App\Http\Requests;

use App\Enums\Status;
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
        $inventoryItemId = optional($this->route('inventoryItem'))->id;

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
            'quantity' => ['required', 'numeric', 'min:0', 'max:9999999999999999.999'],
            'unit' => ['required', 'string', 'max:50'],
            'minimum_stock' => ['required', 'numeric', 'min:0', 'max:9999999999999999.999'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.999999'],
            'supplier' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'integer', Rule::in([Status::ACTIVE, Status::INACTIVE])],
        ];
    }
}
