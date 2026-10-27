<?php

namespace App\Services;

use App\Http\Requests\ItemIngredientRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryItem;
use App\Models\Item;
use App\Models\ItemExtra;
use App\Models\ItemIngredient;
use App\Models\ItemVariation;
use Exception;
use Illuminate\Support\Facades\Log;

class ItemIngredientService
{
    /**
     * @throws Exception
     */
    public function list(Item $item)
    {
        try {
            return ItemIngredient::with('inventoryItem')
                ->where('item_id', $item->id)
                ->orderBy('id')
                ->get();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * What the recipe screen needs besides the lines: the item's variations and extras (the parts of a recipe)
     * and the inventory items that can be used as ingredients.
     *
     * @throws Exception
     */
    public function options(Item $item): array
    {
        try {
            return [
                'item' => ['id' => $item->id, 'name' => $item->name, 'price' => $item->price],
                'variations' => ItemVariation::with('itemAttribute')
                    ->where('item_id', $item->id)
                    ->orderBy('item_attribute_id')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (ItemVariation $variation) => [
                        'id' => $variation->id,
                        'name' => $variation->name,
                        'attribute' => optional($variation->itemAttribute)->name ?? '',
                        'price' => $variation->price,
                        'status' => $variation->status,
                    ])
                    ->values(),
                'extras' => ItemExtra::where('item_id', $item->id)
                    ->orderBy('id')
                    ->get()
                    ->map(fn (ItemExtra $extra) => [
                        'id' => $extra->id,
                        'name' => $extra->name,
                        'price' => $extra->price,
                        'status' => $extra->status,
                    ])
                    ->values(),
                'inventory_items' => InventoryItem::orderBy('name')
                    ->get(['id', 'name', 'unit', 'unit_cost', 'status']),
            ];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(ItemIngredientRequest $request, Item $item): ItemIngredient
    {
        try {
            return ItemIngredient::create($this->attributes($request) + ['item_id' => $item->id])->load('inventoryItem');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(ItemIngredientRequest $request, Item $item, ItemIngredient $itemIngredient): ItemIngredient
    {
        $this->ensureBelongs($item, $itemIngredient);

        try {
            $itemIngredient->update($this->attributes($request));

            return $itemIngredient->load('inventoryItem');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Item $item, ItemIngredient $itemIngredient): void
    {
        $this->ensureBelongs($item, $itemIngredient);

        try {
            $itemIngredient->delete();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function attributes(ItemIngredientRequest $request): array
    {
        return [
            'item_variation_id' => $request->input('item_variation_id') ?: null,
            'item_extra_id' => $request->input('item_extra_id') ?: null,
            'inventory_item_id' => $request->input('inventory_item_id'),
            'quantity' => round((float) $request->input('quantity'), 3),
        ];
    }

    /**
     * @throws Exception
     */
    private function ensureBelongs(Item $item, ItemIngredient $itemIngredient): void
    {
        if ($itemIngredient->item_id !== $item->id) {
            throw new Exception(trans('all.message.ingredient_not_in_item'), 404);
        }
    }
}
