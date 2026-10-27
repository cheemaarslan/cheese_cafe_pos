<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Http\Requests\InventoryWastageRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\InventoryUnitLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryItem;
use App\Models\InventoryWastage;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryWastageService
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
            $query = InventoryWastage::with('inventoryItem', 'creator');

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->input('from_date'))));
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->input('to_date'))));
            }
            if ($request->filled('inventory_item_id')) {
                $query->where('inventory_item_id', $request->integer('inventory_item_id'));
            }
            if ($request->filled('reason')) {
                $query->where('reason', $request->integer('reason'));
            }

            return $query->orderByDesc('id')->paginate($request->integer('per_page', 10));
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Record stock thrown away and take it out of the item's stock.
     *
     * @throws Exception
     */
    public function store(InventoryWastageRequest $request): InventoryWastage
    {
        try {
            return DB::transaction(function () use ($request) {
                $inventoryItem = InventoryItem::findOrFail($request->input('inventory_item_id'));
                $quantity = InventoryUnitLibrary::toBase($request->input('quantity'), $request->input('unit'));

                $wastage = InventoryWastage::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'quantity' => $quantity,
                    'unit_cost' => $inventoryItem->unit_cost,
                    'reason' => $request->integer('reason'),
                    'note' => $request->input('note'),
                    'created_by' => Auth::id(),
                ]);

                $this->inventoryStockService->move($inventoryItem, -$quantity, InventoryTransactionType::WASTAGE, [
                    'note' => trim('#' . $wastage->id . ' ' . $request->input('note')),
                ]);

                return $wastage->load('inventoryItem', 'creator');
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Undo a wrongly entered wastage: its stock goes back with a new ledger entry, then the record is removed.
     *
     * @throws Exception
     */
    public function destroy(InventoryWastage $inventoryWastage): void
    {
        try {
            DB::transaction(function () use ($inventoryWastage) {
                $inventoryItem = InventoryItem::withTrashed()->findOrFail($inventoryWastage->inventory_item_id);

                $this->inventoryStockService->move($inventoryItem, (float) $inventoryWastage->quantity, InventoryTransactionType::WASTAGE, [
                    'note' => '#' . $inventoryWastage->id . ' (deleted)',
                    'unit_cost' => $inventoryWastage->unit_cost,
                ]);

                $inventoryWastage->delete();
            });
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
