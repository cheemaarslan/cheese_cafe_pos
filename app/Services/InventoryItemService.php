<?php

namespace App\Services;

use App\Http\Requests\InventoryItemRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryItemService
{
    private const SORTABLE_COLUMNS = [
        'id',
        'name',
        'sku',
        'category',
        'quantity',
        'minimum_stock',
        'unit_cost',
        'supplier',
        'status',
        'created_at',
    ];

    public function list(PaginateRequest $request)
    {
        try {
            $query = InventoryItem::query();

            foreach (['name', 'sku', 'category', 'unit', 'supplier'] as $field) {
                if ($request->filled($field)) {
                    $query->where($field, 'like', '%' . $request->input($field) . '%');
                }
            }

            if ($request->filled('status')) {
                $query->where('status', $request->integer('status'));
            }

            if ($request->boolean('low_stock')) {
                $query->whereColumn('quantity', '<=', 'minimum_stock');
            }

            $orderColumn = in_array($request->input('order_column'), self::SORTABLE_COLUMNS, true)
                ? $request->input('order_column')
                : 'id';
            $orderType = strtolower((string) $request->input('order_type')) === 'asc' ? 'asc' : 'desc';

            $query->orderBy($orderColumn, $orderType);

            return $request->boolean('paginate')
                ? $query->paginate($request->integer('per_page', 10))
                : $query->get();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function store(InventoryItemRequest $request): InventoryItem
    {
        try {
            return DB::transaction(function () use ($request) {
                $inventoryItem = InventoryItem::create($request->validated());
                $this->recordPurchase($inventoryItem, (float) $inventoryItem->quantity);

                return $inventoryItem;
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function update(InventoryItemRequest $request, InventoryItem $inventoryItem): InventoryItem
    {
        try {
            return DB::transaction(function () use ($request, $inventoryItem) {
                $previousQuantity = (float) $inventoryItem->quantity;
                $inventoryItem->update($request->validated());

                // Only an increase is new stock bought; a decrease is stock being used up.
                $this->recordPurchase($inventoryItem, round((float) $inventoryItem->quantity - $previousQuantity, 3));

                return $inventoryItem->refresh();
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Log newly bought stock so the Profit & Loss report counts its cost as an expense.
     */
    private function recordPurchase(InventoryItem $inventoryItem, float $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $unitCost = (float) ($inventoryItem->unit_cost ?? 0);

        InventoryPurchase::create([
            'inventory_item_id' => $inventoryItem->id,
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => round($quantity * $unitCost, 6),
            'purchase_date' => now()->toDateString(),
            'created_by' => Auth::id(),
        ]);
    }

    public function destroy(InventoryItem $inventoryItem): void
    {
        try {
            $inventoryItem->delete();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
