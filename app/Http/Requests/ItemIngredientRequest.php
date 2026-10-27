<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $item = $this->route('item');
        $itemIngredient = $this->route('itemIngredient');
        $variationId = $this->input('item_variation_id');
        $extraId = $this->input('item_extra_id');

        return [
            'item_variation_id' => [
                'nullable',
                'integer',
                'prohibits:item_extra_id',
                Rule::exists('item_variations', 'id')->where('item_id', $item->id)->whereNull('deleted_at'),
            ],
            'item_extra_id' => [
                'nullable',
                'integer',
                Rule::exists('item_extras', 'id')->where('item_id', $item->id)->whereNull('deleted_at'),
            ],
            'inventory_item_id' => [
                'required',
                'integer',
                Rule::exists('inventory_items', 'id')->whereNull('deleted_at'),
                // Once per part of the recipe: change the quantity of the existing line instead.
                Rule::unique('item_ingredients', 'inventory_item_id')
                    ->where(function ($query) use ($item, $variationId, $extraId) {
                        $query->where('item_id', $item->id);
                        $variationId ? $query->where('item_variation_id', $variationId) : $query->whereNull('item_variation_id');
                        $extraId ? $query->where('item_extra_id', $extraId) : $query->whereNull('item_extra_id');
                    })
                    ->ignore(optional($itemIngredient)->id),
            ],
            'quantity' => ['required', 'numeric', 'gt:0', 'max:9999999999.999'],
        ];
    }

    public function messages(): array
    {
        return [
            'inventory_item_id.unique' => trans('all.message.ingredient_already_in_recipe'),
        ];
    }
}
