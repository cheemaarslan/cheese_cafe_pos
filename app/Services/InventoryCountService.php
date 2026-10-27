<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Http\Requests\InventoryCountRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\InventoryUnitLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\InventoryItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryCountService
{
    public function __construct(private InventoryStockService $inventoryStockService)
    {
    }

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $query = InventoryCount::with('creator')
                ->withCount([
                    'items',
                    'items as changed_items_count' => fn ($query) => $query->where('difference', '!=', 0),
                ])
                ->addSelect(['difference_value' => InventoryCountItem::selectRaw('COALESCE(SUM(difference * COALESCE(unit_cost, 0)), 0)')
                    ->whereColumn('inventory_count_id', 'inventory_counts.id')]);

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->input('from_date'))));
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->input('to_date'))));
            }

            return $query->orderByDesc('id')->paginate($request->integer('per_page', 10));
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function show(InventoryCount $inventoryCount): InventoryCount
    {
        return $inventoryCount->load('items.inventoryItem', 'creator');
    }

    /**
     * Set each counted item's stock to what was found. The difference from the system's stock at this moment
     * (so orders placed while counting are allowed for) is booked as a STOCK_COUNT ledger entry.
     *
     * @throws Exception
     */
    public function store(InventoryCountRequest $request): InventoryCount
    {
        try {
            return DB::transaction(function () use ($request) {
                $inventoryCount = InventoryCount::create([
                    'note' => $request->input('note'),
                    'created_by' => Auth::id(),
                ]);

                // Ascending ids, so this locks stock rows in the same order as orders do
                $lines = collect($request->input('items'))->sortBy('inventory_item_id');

                foreach ($lines as $line) {
                    $inventoryItem = InventoryItem::lockForUpdate()->findOrFail($line['inventory_item_id']);
                    $system = (float) $inventoryItem->quantity;
                    $counted = InventoryUnitLibrary::toBase($line['counted'], $inventoryItem->purchase_unit);
                    $difference = round($counted - $system, 3);

                    InventoryCountItem::create([
                        'inventory_count_id' => $inventoryCount->id,
                        'inventory_item_id' => $inventoryItem->id,
                        'system_quantity' => $system,
                        'counted_quantity' => $counted,
                        'difference' => $difference,
                        'unit_cost' => $inventoryItem->unit_cost,
                    ]);

                    if ($difference != 0) {
                        $this->inventoryStockService->move($inventoryItem, $difference, InventoryTransactionType::STOCK_COUNT, [
                            'note' => '#' . $inventoryCount->id,
                        ]);
                    }
                }

                return $this->show($inventoryCount);
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
