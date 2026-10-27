<?php

namespace App\Services;

use App\Enums\InventoryDeduction;
use App\Enums\InventoryTransactionType;
use App\Enums\OrderStatus;
use App\Enums\Source;
use App\Models\InventoryItem;
use App\Models\ItemIngredient;
use App\Models\Order;
use Illuminate\Database\DeadlockException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Takes POS orders' recipe ingredients out of inventory and keeps that right as the order changes:
 * deducted when the order is placed (POS orders start CONFIRMED), corrected when its items are edited, put back
 * when it's canceled, rejected, returned or deleted, and deducted again if it's brought back.
 *
 * What an order has used is read from its stock ledger rows (inventory_transactions.order_id), so a correction
 * only moves the difference. Inventory problems are logged and never stop the order itself from being saved.
 */
class OrderInventoryService
{
    // Statuses in which the order's ingredients count as used
    private const USED_STATUSES = [
        OrderStatus::CONFIRMED,
        OrderStatus::PREPARING,
        OrderStatus::PREPARED,
        OrderStatus::OUT_FOR_DELIVERY,
        OrderStatus::DELIVERED,
    ];

    // Statuses in which they go back into stock
    private const RETURN_STATUSES = [
        OrderStatus::CANCELED,
        OrderStatus::REJECTED,
        OrderStatus::RETURNED,
    ];

    public function __construct(private InventoryStockService $inventoryStockService)
    {
    }

    /**
     * A POS order was placed, with its items saved.
     */
    public function orderPlaced(Order $order): void
    {
        if ($this->isPos($order) && $order->inventory_deduction === null
            && in_array((int) $order->status, self::USED_STATUSES, true)) {
            $this->safely($order, fn () => $this->deduct($order, InventoryTransactionType::ORDER));
        }
    }

    /**
     * A POS order's items were rewritten (edited in place): move only the difference from what it used before.
     */
    public function orderUpdated(Order $order): void
    {
        if ($this->isPos($order) && (int) $order->inventory_deduction === InventoryDeduction::DEDUCTED) {
            $this->safely($order, fn () => $this->deduct($order, InventoryTransactionType::ORDER_UPDATE));
        }
    }

    /**
     * A POS order's status changed: put stock back when it's canceled, rejected or returned, and take it out
     * again if such an order is brought back.
     */
    public function statusChanged(Order $order): void
    {
        if (! $this->isPos($order)) {
            return;
        }

        $status = (int) $order->status;
        $deduction = $order->inventory_deduction === null ? null : (int) $order->inventory_deduction;

        if ($deduction === InventoryDeduction::DEDUCTED && in_array($status, self::RETURN_STATUSES, true)) {
            $this->safely($order, fn () => $this->putBack($order, '#' . $order->order_serial_no));
        } elseif ($deduction === InventoryDeduction::RETURNED && in_array($status, self::USED_STATUSES, true)) {
            $this->safely($order, fn () => $this->deduct($order, InventoryTransactionType::ORDER));
        }
    }

    /**
     * A POS order is about to be deleted: put back what it used.
     */
    public function orderDeleting(Order $order): void
    {
        if ($this->isPos($order) && (int) $order->inventory_deduction === InventoryDeduction::DEDUCTED) {
            $this->safely($order, fn () => $this->putBack($order, '#' . $order->order_serial_no . ' (deleted)'));
        }
    }

    /**
     * Bring the stock this order has taken in line with what its items need now, and mark it deducted.
     * Deleted inventory items are left alone, even if a recipe still lists them.
     */
    private function deduct(Order $order, int $type): void
    {
        DB::transaction(function () use ($order, $type) {
            $needed = $this->ingredientsNeeded($order);
            $used = $this->usedSoFar($order);
            $ids = $this->sortedIds($needed, $used);
            $deletedIds = InventoryItem::onlyTrashed()->whereIn('id', $ids)->pluck('id')->all();

            foreach (array_diff($ids, $deletedIds) as $inventoryItemId) {
                $change = round(($needed[$inventoryItemId] ?? 0) - ($used[$inventoryItemId] ?? 0), 3);
                if ($change != 0) {
                    $this->move($order, $inventoryItemId, -$change, $type, '#' . $order->order_serial_no);
                }
            }

            $this->mark($order, InventoryDeduction::DEDUCTED);
        });
    }

    /**
     * Return everything this order has taken to stock.
     */
    private function putBack(Order $order, string $note): void
    {
        DB::transaction(function () use ($order, $note) {
            $used = $this->usedSoFar($order);

            foreach ($this->sortedIds($used) as $inventoryItemId) {
                if ($used[$inventoryItemId] != 0) {
                    $this->move($order, $inventoryItemId, $used[$inventoryItemId], InventoryTransactionType::ORDER_RETURN, $note);
                }
            }

            $this->mark($order, InventoryDeduction::RETURNED);
        });
    }

    /**
     * Base-unit amount of each inventory item the order's items need now: for every item line, its quantity times
     * the item's every-order recipe lines plus those of the variations and extras chosen on that line.
     *
     * @return array<int, float> inventory_item_id => quantity
     */
    private function ingredientsNeeded(Order $order): array
    {
        $orderItems = DB::table('order_items')
            ->where('order_id', $order->id)
            ->whereNotNull('item_id')
            ->get(['item_id', 'quantity', 'item_variations', 'item_extras']);

        if ($orderItems->isEmpty()) {
            return [];
        }

        $recipes = ItemIngredient::whereIn('item_id', $orderItems->pluck('item_id')->unique())->get()->groupBy('item_id');

        $needed = [];
        foreach ($orderItems as $orderItem) {
            $variationIds = $this->chosenIds($orderItem->item_variations);
            $extraIds = $this->chosenIds($orderItem->item_extras);

            foreach ($recipes->get($orderItem->item_id, []) as $line) {
                $applies = $line->item_variation_id
                    ? in_array((int) $line->item_variation_id, $variationIds, true)
                    : ($line->item_extra_id ? in_array((int) $line->item_extra_id, $extraIds, true) : true);

                if ($applies) {
                    $needed[$line->inventory_item_id] = ($needed[$line->inventory_item_id] ?? 0)
                        + (float) $orderItem->quantity * (float) $line->quantity;
                }
            }
        }

        return array_map(fn ($quantity) => round($quantity, 3), $needed);
    }

    /**
     * Base-unit amount of each inventory item this order has taken so far, from its ledger rows.
     *
     * @return array<int, float> inventory_item_id => quantity
     */
    private function usedSoFar(Order $order): array
    {
        return DB::table('inventory_transactions')
            ->where('order_id', $order->id)
            ->groupBy('inventory_item_id')
            ->selectRaw('inventory_item_id, -SUM(quantity) as used')
            ->pluck('used', 'inventory_item_id')
            ->map(fn ($used) => round((float) $used, 3))
            ->all();
    }

    /**
     * Ids of the variations or extras chosen on an order line, from its JSON ([{"id": 5, ...}, ...]).
     */
    private function chosenIds($json): array
    {
        $chosen = is_string($json) ? json_decode($json, true) : $json;
        if (! is_array($chosen)) {
            return [];
        }

        $ids = [];
        foreach ($chosen as $entry) {
            $id = is_array($entry) ? ($entry['id'] ?? null) : (is_object($entry) ? ($entry->id ?? null) : null);
            if ($id) {
                $ids[] = (int) $id;
            }
        }

        return $ids;
    }

    private function move(Order $order, int $inventoryItemId, float $quantity, int $type, string $note): void
    {
        $inventoryItem = InventoryItem::withTrashed()->find($inventoryItemId);
        if ($inventoryItem) {
            $this->inventoryStockService->move($inventoryItem, $quantity, $type, ['order_id' => $order->id, 'note' => $note]);
        }
    }

    /**
     * Inventory item ids in ascending order, so concurrent orders always lock stock rows in the same order.
     */
    private function sortedIds(array ...$maps): array
    {
        $ids = array_unique(array_merge(...array_map('array_keys', $maps)));
        sort($ids);

        return $ids;
    }

    private function mark(Order $order, int $deduction): void
    {
        // Straight to the table: no order events, timestamps or branch scope involved.
        DB::table('orders')->where('id', $order->id)->update(['inventory_deduction' => $deduction]);
        $order->inventory_deduction = $deduction;
        $order->syncOriginalAttribute('inventory_deduction');
    }

    private function isPos(Order $order): bool
    {
        return (int) $order->source === Source::POS;
    }

    /**
     * Run an inventory change for an order; if it fails, log it and let the order carry on. A deadlock is passed
     * on, since it has already rolled back the whole order transaction.
     */
    private function safely(Order $order, callable $change): void
    {
        try {
            $change();
        } catch (DeadlockException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Inventory update failed for order #' . $order->id . ': ' . $exception->getMessage());
        }
    }
}
