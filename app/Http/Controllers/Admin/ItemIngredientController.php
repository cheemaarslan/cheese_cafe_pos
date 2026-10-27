<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ItemIngredientRequest;
use App\Http\Resources\ItemIngredientResource;
use App\Models\Item;
use App\Models\ItemIngredient;
use App\Services\ItemIngredientService;
use Exception;

class ItemIngredientController extends AdminController
{
    public function __construct(private ItemIngredientService $itemIngredientService)
    {
        parent::__construct();
        $this->middleware(['permission:items_show'])->only('index', 'store', 'update', 'destroy');
    }

    public function index(Item $item)
    {
        try {
            return ItemIngredientResource::collection($this->itemIngredientService->list($item))
                ->additional($this->itemIngredientService->options($item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ItemIngredientRequest $request, Item $item)
    {
        try {
            return new ItemIngredientResource($this->itemIngredientService->store($request, $item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ItemIngredientRequest $request, Item $item, ItemIngredient $itemIngredient)
    {
        try {
            return new ItemIngredientResource($this->itemIngredientService->update($request, $item, $itemIngredient));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Item $item, ItemIngredient $itemIngredient)
    {
        try {
            $this->itemIngredientService->destroy($item, $itemIngredient);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
