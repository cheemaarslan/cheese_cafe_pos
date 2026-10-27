<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Status;
use App\Http\Requests\InventoryCountRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\InventoryCountResource;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Services\InventoryCountService;
use Exception;

class InventoryCountController extends AdminController
{
    public function __construct(private InventoryCountService $inventoryCountService)
    {
        parent::__construct();
        $this->middleware(['permission:stock-counts'])->only('index');
        $this->middleware(['permission:stock_counts_create'])->only('items', 'store');
        $this->middleware(['permission:stock_counts_show'])->only('show');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return InventoryCountResource::collection($this->inventoryCountService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    /**
     * Active inventory items with their current stock, to count.
     */
    public function items()
    {
        return InventoryItemResource::collection(InventoryItem::where('status', Status::ACTIVE)->orderBy('name')->get());
    }

    public function show(InventoryCount $inventoryCount)
    {
        return new InventoryCountResource($this->inventoryCountService->show($inventoryCount));
    }

    public function store(InventoryCountRequest $request)
    {
        try {
            return new InventoryCountResource($this->inventoryCountService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
