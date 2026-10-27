<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Requests\InventoryWastageRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\InventoryItemResource;
use App\Http\Resources\InventoryWastageResource;
use App\Models\InventoryItem;
use App\Models\InventoryWastage;
use App\Services\InventoryWastageService;
use Exception;

class InventoryWastageController extends AdminController
{
    public function __construct(private InventoryWastageService $inventoryWastageService)
    {
        parent::__construct();
        $this->middleware(['permission:inventory-wastage'])->only('index', 'items');
        $this->middleware(['permission:inventory_wastage_create'])->only('store');
        $this->middleware(['permission:inventory_wastage_delete'])->only('destroy');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return InventoryWastageResource::collection($this->inventoryWastageService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Active inventory items, to choose what was wasted.
     */
    public function items()
    {
        return InventoryItemResource::collection(InventoryItem::where('status', Status::ACTIVE)->orderBy('name')->get());
    }

    public function store(InventoryWastageRequest $request)
    {
        try {
            return new InventoryWastageResource($this->inventoryWastageService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(InventoryWastage $inventoryWastage)
    {
        try {
            $this->inventoryWastageService->destroy($inventoryWastage);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
