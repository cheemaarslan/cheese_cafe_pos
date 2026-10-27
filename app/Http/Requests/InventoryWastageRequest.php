<?php

namespace App\Http\Requests;

use App\Enums\WastageReason;
use App\Libraries\InventoryUnitLibrary;
use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryWastageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventory_item_id' => ['required', 'integer', Rule::exists('inventory_items', 'id')->whereNull('deleted_at')],
            // The unit the quantity is entered in (kg or g for a weighed item...), converted to the item's base unit
            'unit' => ['required', 'string', Rule::in(InventoryUnitLibrary::units())],
            'quantity' => ['required', 'numeric', 'gt:0', 'max:9999999999.999'],
            'reason' => ['required', 'integer', Rule::in([
                WastageReason::SPOILED,
                WastageReason::DAMAGED,
                WastageReason::KITCHEN_ERROR,
                WastageReason::OTHER,
            ])],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $inventoryItem = InventoryItem::find($this->input('inventory_item_id'));
            if ($inventoryItem && InventoryUnitLibrary::baseUnit($this->input('unit')) !== $inventoryItem->unit) {
                $validator->errors()->add('unit', trans('all.message.inventory_unit_mismatch'));
            }
        });
    }
}
