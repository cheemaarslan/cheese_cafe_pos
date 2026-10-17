<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Http\Requests\InventoryItemRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\InventoryUnitLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryItem;
use App\Models\InventoryPurchase;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryItemService
{
    public function __construct(private InventoryStockService $inventoryStockService)
    {
    }

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
                $inventoryItem = InventoryItem::create($this->attributes($request) + ['quantity' => 0]);

                $quantity = InventoryUnitLibrary::toBase($request->input('quantity'), $inventoryItem->purchase_unit);
                if ($quantity > 0) {
                    $this->inventoryStockService->move($inventoryItem, $quantity, InventoryTransactionType::PURCHASE);
                    $this->recordPurchase($inventoryItem, $quantity, $request);
                }

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
                $inventoryItem = InventoryItem::lockForUpdate()->findOrFail($inventoryItem->id);
                $previousUnit = $inventoryItem->unit;
                $previousPurchaseUnit = $inventoryItem->purchase_unit;
                $inventoryItem->update($this->attributes($request));

                if ($inventoryItem->unit !== $previousUnit) {
                    // Switched to another kind of unit (e.g. pcs to litre): the old count can't be converted,
                    // so the quantity entered becomes the stock.
                    $this->inventoryStockService->move(
                        $inventoryItem,
                        InventoryUnitLibrary::toBase($request->input('quantity'), $inventoryItem->purchase_unit)
                            - (float) $inventoryItem->quantity,
                        InventoryTransactionType::ADJUSTMENT,
                        ['note' => InventoryUnitLibrary::label($previousPurchaseUnit) . ' → ' . InventoryUnitLibrary::label($inventoryItem->purchase_unit)]
                    );
                } else {
                    // Added to the current stock, so if orders took it below zero, what they used comes out
                    // of the new stock (-0.1 kg + 5 kg bought = 4.9 kg).
                    $added = InventoryUnitLibrary::toBase($request->input('add_quantity'), $inventoryItem->purchase_unit);

                    // Stock added is new stock bought; stock removed is a manual adjustment.
                    if ($added > 0) {
                        $this->inventoryStockService->move($inventoryItem, $added, InventoryTransactionType::PURCHASE);
                        $this->recordPurchase($inventoryItem, $added, $request);
                    } elseif ($added < 0) {
                        $this->inventoryStockService->move($inventoryItem, $added, InventoryTransactionType::ADJUSTMENT);
                    }
                }

                return $inventoryItem->refresh();
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function transactions(PaginateRequest $request, InventoryItem $inventoryItem)
    {
        try {
            return $inventoryItem->transactions()
                ->with('creator', 'inventoryItem')
                ->orderByDesc('id')
                ->paginate($request->integer('per_page', 10));
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * The item's fields from the form, with amounts entered per purchase unit (kg, litre...) converted to
     * the base unit. Stock quantity is left out: it only changes through InventoryStockService.
     */
    private function attributes(InventoryItemRequest $request): array
    {
        $purchaseUnit = $request->input('purchase_unit');
        $unitCost = $request->input('unit_cost');

        return Arr::only($request->validated(), ['name', 'sku', 'category', 'supplier', 'notes', 'status']) + [
            'unit' => InventoryUnitLibrary::baseUnit($purchaseUnit),
            'purchase_unit' => $purchaseUnit,
            'minimum_stock' => InventoryUnitLibrary::toBase($request->input('minimum_stock'), $purchaseUnit),
            'unit_cost' => $unitCost === null || $unitCost === ''
                ? null
                : InventoryUnitLibrary::costToBase($unitCost, $purchaseUnit),
        ];
    }

    /**
     * Log newly bought stock ($quantity in the base unit) so the Profit & Loss report counts its cost as
     * an expense.
     */
    private function recordPurchase(InventoryItem $inventoryItem, float $quantity, InventoryItemRequest $request): void
    {
        // Priced from the cost as entered (Rs 1250 per kg) rather than the per-gram cost, which is rounded.
        $totalCost = InventoryUnitLibrary::fromBase($quantity, $inventoryItem->purchase_unit)
            * (float) $request->input('unit_cost', 0);

        InventoryPurchase::create([
            'inventory_item_id' => $inventoryItem->id,
            'quantity' => $quantity,
            'unit_cost' => (float) ($inventoryItem->unit_cost ?? 0),
            'total_cost' => round($totalCost, 6),
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
