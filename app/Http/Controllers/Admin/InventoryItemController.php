<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InventoryItemRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventoryTransactionResource;
use App\Models\InventoryItem;
use App\Services\InventoryItemService;
use Exception;

class InventoryItemController extends AdminController
{
    public function __construct(private InventoryItemService $inventoryItemService)
    {
        parent::__construct();
        $this->middleware(['permission:inventory'])->only('index');
        $this->middleware(['permission:inventory_create'])->only('store');
        $this->middleware(['permission:inventory_edit'])->only('update');
        $this->middleware(['permission:inventory_delete'])->only('destroy');
        $this->middleware(['permission:inventory_show'])->only('show', 'transactions');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return InventoryItemResource::collection($this->inventoryItemService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(InventoryItemRequest $request)
    {
        try {
            return new InventoryItemResource($this->inventoryItemService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(InventoryItem $inventoryItem)
    {
        return new InventoryItemResource($inventoryItem);
    }

    public function transactions(PaginateRequest $request, InventoryItem $inventoryItem)
    {
        try {
            return InventoryTransactionResource::collection(
                $this->inventoryItemService->transactions($request, $inventoryItem)
            );
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(InventoryItemRequest $request, InventoryItem $inventoryItem)
    {
        try {
            return new InventoryItemResource($this->inventoryItemService->update($request, $inventoryItem));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            $this->inventoryItemService->destroy($inventoryItem);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
