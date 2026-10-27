<?php

namespace App\Services;

use App\Enums\InventoryTransactionType as Type;
use App\Libraries\AppLibrary;
use App\Libraries\InventoryUnitLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Stock movement of every inventory item over a date range, from the stock ledger: opening stock, what came in
 * and went out (by kind) and closing stock, with the cost of what orders used and what was wasted.
 */
class InventoryReportService
{
    // Report column => the ledger types it adds up
    private const COLUMNS = [
        'purchased' => [Type::PURCHASE],
        'used' => [Type::ORDER, Type::ORDER_UPDATE, Type::ORDER_RETURN],
        'wasted' => [Type::WASTAGE],
        'counted' => [Type::STOCK_COUNT],
        'adjusted' => [Type::OPENING, Type::ADJUSTMENT],
    ];

    // Longest date range the day-by-day view covers
    private const MAX_DAYS = 366;

    /**
     * @throws Exception
     */
    public function report(Request $request): array
    {
        try {
            [$from, $to] = $this->period($request);

            $inPeriod = 'created_at BETWEEN ? AND ?';
            $selects = [
                'inventory_item_id',
                DB::raw('SUM(CASE WHEN created_at < ? THEN quantity ELSE 0 END) AS opening'),
                DB::raw("SUM(CASE WHEN {$inPeriod} THEN 1 ELSE 0 END) AS entries"),
            ];
            $bindings = [$from, $from, $to];
            foreach (self::COLUMNS as $column => $types) {
                $typeList = implode(',', array_map('intval', $types));
                $selects[] = DB::raw("SUM(CASE WHEN {$inPeriod} AND type IN ({$typeList}) THEN quantity ELSE 0 END) AS {$column}");
                $selects[] = DB::raw("SUM(CASE WHEN {$inPeriod} AND type IN ({$typeList}) THEN quantity * COALESCE(unit_cost, 0) ELSE 0 END) AS {$column}_value");
                array_push($bindings, $from, $to, $from, $to);
            }

            $movements = DB::table('inventory_transactions')
                ->select($selects)
                ->addBinding($bindings, 'select')
                ->where('created_at', '<=', $to)
                ->groupBy('inventory_item_id')
                ->get()
                ->keyBy('inventory_item_id');

            // Every current item, plus deleted ones with stock entries in these dates (kept for the record only)
            $deletedWithEntries = $movements->filter(fn ($movement) => $movement->entries > 0)->keys();
            $inventoryItems = InventoryItem::withTrashed()
                ->where(function ($query) use ($deletedWithEntries) {
                    $query->whereNull('deleted_at')->orWhereIn('id', $deletedWithEntries);
                })
                ->when($request->filled('name'), fn ($query) => $query->where('name', 'like', '%' . $request->input('name') . '%'))
                ->when($request->filled('category'), fn ($query) => $query->where('category', 'like', '%' . $request->input('category') . '%'))
                ->orderBy('name')
                ->get();

            $rows = [];
            $totals = ['used_value' => 0.0, 'wasted_value' => 0.0, 'counted_value' => 0.0, 'closing_value' => 0.0];

            foreach ($inventoryItems as $inventoryItem) {
                $movement = $movements->get($inventoryItem->id);
                $opening = (float) optional($movement)->opening;
                $base = [];
                $closing = $opening;
                foreach (array_keys(self::COLUMNS) as $column) {
                    $base[$column] = (float) optional($movement)->{$column};
                    $closing += $base[$column];
                }

                // Used and wasted are shown as positive amounts taken out. What a deleted item has left is no
                // longer stock, so it adds nothing to the stock value.
                $deleted = $inventoryItem->trashed();
                $values = [
                    'used_value' => -(float) optional($movement)->used_value,
                    'wasted_value' => -(float) optional($movement)->wasted_value,
                    'counted_value' => (float) optional($movement)->counted_value,
                    'closing_value' => $deleted ? 0.0 : $closing * (float) $inventoryItem->unit_cost,
                ];
                foreach ($values as $key => $value) {
                    $totals[$key] += $value;
                }

                $rows[] = [
                    'id' => $inventoryItem->id,
                    'name' => $inventoryItem->name,
                    'category' => $inventoryItem->category ?? '',
                    'deleted' => $deleted,
                    'unit' => InventoryUnitLibrary::label($inventoryItem->purchase_unit),
                ] + $this->quantities($opening, $base, $closing, $inventoryItem->purchase_unit) + [
                    'is_low_stock' => ! $deleted && $closing <= (float) $inventoryItem->minimum_stock,
                ] + array_map(fn ($value) => round($value, 6), $values) + [
                    'used_value_currency_price' => AppLibrary::currencyAmountFormat($values['used_value']),
                    'wasted_value_currency_price' => AppLibrary::currencyAmountFormat($values['wasted_value']),
                    'counted_value_currency_price' => AppLibrary::currencyAmountFormat($values['counted_value']),
                ];
            }

            return [
                'from_date' => $from->toDateString(),
                'to_date' => $to->toDateString(),
                'rows' => $rows,
                'summary' => array_map(fn ($value) => round($value, 6), $totals) + [
                    'used_value_currency_price' => AppLibrary::currencyAmountFormat($totals['used_value']),
                    'wasted_value_currency_price' => AppLibrary::currencyAmountFormat($totals['wasted_value']),
                    'counted_value_currency_price' => AppLibrary::currencyAmountFormat($totals['counted_value']),
                    'closing_value_currency_price' => AppLibrary::currencyAmountFormat($totals['closing_value']),
                ],
            ];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * One inventory item day by day over a date range: each day's opening stock (the day before's closing), what
     * came in and went out, and closing stock. Works for deleted items too, for their history.
     *
     * @throws Exception
     */
    public function daily(Request $request, InventoryItem $inventoryItem): array
    {
        [$from, $to] = $this->period($request);
        if ($from->diffInDays($to) >= self::MAX_DAYS) {
            throw new Exception(trans('all.message.inventory_daily_range_limit', ['days' => self::MAX_DAYS]), 422);
        }

        try {
            $opening = (float) DB::table('inventory_transactions')
                ->where('inventory_item_id', $inventoryItem->id)
                ->where('created_at', '<', $from)
                ->sum('quantity');

            $selects = [DB::raw('DATE(created_at) AS day')];
            foreach (self::COLUMNS as $column => $types) {
                $typeList = implode(',', array_map('intval', $types));
                $selects[] = DB::raw("SUM(CASE WHEN type IN ({$typeList}) THEN quantity ELSE 0 END) AS {$column}");
                $selects[] = DB::raw("SUM(CASE WHEN type IN ({$typeList}) THEN quantity * COALESCE(unit_cost, 0) ELSE 0 END) AS {$column}_value");
            }

            $days = DB::table('inventory_transactions')
                ->select($selects)
                ->where('inventory_item_id', $inventoryItem->id)
                ->whereBetween('created_at', [$from, $to])
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get()
                ->keyBy('day');

            $unit = $inventoryItem->purchase_unit;
            $rows = [];
            $totals = array_fill_keys(array_keys(self::COLUMNS), 0.0);
            $values = ['used_value' => 0.0, 'wasted_value' => 0.0];
            $balance = $opening;

            for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
                $movement = $days->get($day->toDateString());
                $dayOpening = $balance;
                $base = [];
                foreach (array_keys(self::COLUMNS) as $column) {
                    $base[$column] = (float) optional($movement)->{$column};
                    $totals[$column] += $base[$column];
                    $balance += $base[$column];
                }

                $dayValues = [
                    'used_value' => -(float) optional($movement)->used_value,
                    'wasted_value' => -(float) optional($movement)->wasted_value,
                ];
                foreach ($dayValues as $key => $value) {
                    $values[$key] += $value;
                }

                $rows[] = [
                    'date' => $day->toDateString(),
                    'converted_date' => AppLibrary::date($day),
                    'has_entries' => $movement !== null,
                ] + $this->quantities($dayOpening, $base, $balance, $unit) + $this->values($dayValues);
            }

            return [
                'item' => [
                    'id' => $inventoryItem->id,
                    'name' => $inventoryItem->name,
                    'unit' => InventoryUnitLibrary::label($unit),
                    'deleted' => $inventoryItem->trashed(),
                ],
                'from_date' => $from->toDateString(),
                'to_date' => $to->toDateString(),
                'rows' => $rows,
                'totals' => $this->quantities($opening, $totals, $balance, $unit) + $this->values($values),
            ];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * The chosen dates as the start of the first day and the end of the last (this month so far by default).
     *
     * @return Carbon[]
     */
    private function period(Request $request): array
    {
        $from = Carbon::parse($request->filled('from_date')
            ? date('Y-m-d', strtotime($request->input('from_date')))
            : now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($request->filled('to_date')
            ? date('Y-m-d', strtotime($request->input('to_date')))
            : now()->toDateString())->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    /**
     * Opening, each column and closing, from base units to the item's purchase unit. Used and wasted come out
     * positive.
     */
    private function quantities(float $opening, array $base, float $closing, string $unit): array
    {
        return [
            'opening' => InventoryUnitLibrary::fromBase($opening, $unit),
            'purchased' => InventoryUnitLibrary::fromBase($base['purchased'], $unit),
            'used' => InventoryUnitLibrary::fromBase(-$base['used'], $unit),
            'wasted' => InventoryUnitLibrary::fromBase(-$base['wasted'], $unit),
            'counted' => InventoryUnitLibrary::fromBase($base['counted'], $unit),
            'adjusted' => InventoryUnitLibrary::fromBase($base['adjusted'], $unit),
            'closing' => InventoryUnitLibrary::fromBase($closing, $unit),
        ];
    }

    /**
     * Used and wasted values, with their currency text.
     */
    private function values(array $values): array
    {
        return [
            'used_value' => round($values['used_value'], 6),
            'wasted_value' => round($values['wasted_value'], 6),
            'used_value_currency_price' => AppLibrary::currencyAmountFormat($values['used_value']),
            'wasted_value_currency_price' => AppLibrary::currencyAmountFormat($values['wasted_value']),
        ];
    }
}
