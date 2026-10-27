<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryCountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('inventory_items', 'id')->whereNull('deleted_at'),
            ],
            // Stock found, in the item's purchase unit (kg, litre...)
            'items.*.counted' => ['required', 'numeric', 'min:0', 'max:9999999999.999'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => trans('all.message.stock_count_empty'),
            'items.min' => trans('all.message.stock_count_empty'),
        ];
    }
}
