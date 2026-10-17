<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Http\Resources\InventoryPurchaseResource;
use App\Libraries\AppLibrary;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Expense;
use App\Models\InventoryPurchase;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfitLossService
{
    /**
     * Orders that count as income: every order that has been accepted or confirmed, paid or not.
     * Pending orders aren't income yet; canceled, rejected and returned orders never are.
     */
    public const INCOME_ORDER_STATUSES = [
        OrderStatus::ACCEPT,
        OrderStatus::CONFIRMED,
        OrderStatus::PREPARING,
        OrderStatus::PREPARED,
        OrderStatus::OUT_FOR_DELIVERY,
        OrderStatus::DELIVERED,
    ];

    // Date ranges longer than this are broken down by month instead of by day.
    private const DAILY_BREAKDOWN_MAX_DAYS = 31;

    /**
     * Profit & Loss = order income - (inventory purchases + general expenses) for a date range.
     *
     * @throws Exception
     */
    public function report(Request $request): array
    {
        try {
            $from = Carbon::parse($request->filled('from_date')
                ? date('Y-m-d', strtotime($request->input('from_date')))
                : now()->startOfMonth()->toDateString())->startOfDay();
            $to = Carbon::parse($request->filled('to_date')
                ? date('Y-m-d', strtotime($request->input('to_date')))
                : now()->toDateString())->endOfDay();

            if ($from->gt($to)) {
                [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
            }

            $byMonth = $from->diffInDays($to) >= self::DAILY_BREAKDOWN_MAX_DAYS;
            $periodFormat = $byMonth ? 'Y-m' : 'Y-m-d';

            $periods = [];
            $cursor = $byMonth ? $from->copy()->startOfMonth() : $from->copy();
            while ($cursor->lte($to)) {
                $periods[$cursor->format($periodFormat)] = [
                    'label' => $byMonth ? $cursor->format('M Y') : AppLibrary::date($cursor),
                    'orders' => 0,
                    'income' => 0.0,
                    'inventory_expense' => 0.0,
                    'general_expense' => 0.0,
                ];
                $byMonth ? $cursor->addMonthNoOverflow() : $cursor->addDay();
            }

            // Order is branch-scoped (BranchScope), so income is for the user's current branch.
            $incomeRows = Order::query()
                ->selectRaw('DATE_FORMAT(order_datetime, ?) as period, SUM(total) as amount, COUNT(*) as orders', [
                    $byMonth ? '%Y-%m' : '%Y-%m-%d',
                ])
                ->whereIn('status', self::INCOME_ORDER_STATUSES)
                ->whereBetween('order_datetime', [$from, $to])
                ->groupBy('period')
                ->get();

            foreach ($incomeRows as $row) {
                if (isset($periods[$row->period])) {
                    $periods[$row->period]['income'] += (float) $row->amount;
                    $periods[$row->period]['orders'] += (int) $row->orders;
                }
            }

            $purchases = InventoryPurchase::with('inventoryItem')
                ->whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()])
                ->orderByDesc('purchase_date')
                ->orderByDesc('id')
                ->get();

            foreach ($purchases as $purchase) {
                $key = $purchase->purchase_date->format($periodFormat);
                if (isset($periods[$key])) {
                    $periods[$key]['inventory_expense'] += (float) $purchase->total_cost;
                }
            }

            $expenses = Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])->get();

            foreach ($expenses as $expense) {
                $key = $expense->expense_date->format($periodFormat);
                if (isset($periods[$key])) {
                    $periods[$key]['general_expense'] += (float) $expense->amount;
                }
            }

            $rows = [];
            foreach ($periods as $period => $values) {
                $rows[] = ['period' => $period, 'label' => $values['label'], 'orders' => $values['orders']]
                    + $this->amounts($values['income'], $values['inventory_expense'], $values['general_expense']);
            }

            $expenseCategories = $expenses
                ->groupBy(fn (Expense $expense) => (string) $expense->category)
                ->map(fn ($group, $category) => [
                    'category' => $category,
                    'amount' => round((float) $group->sum('amount'), 6),
                    'amount_currency_price' => AppLibrary::currencyAmountFormat($group->sum('amount')),
                ])
                ->sortByDesc('amount')
                ->values()
                ->all();

            return [
                'from_date' => $from->toDateString(),
                'to_date' => $to->toDateString(),
                'group_by' => $byMonth ? 'month' : 'day',
                'summary' => ['orders' => array_sum(array_column($periods, 'orders'))] + $this->amounts(
                    array_sum(array_column($periods, 'income')),
                    array_sum(array_column($periods, 'inventory_expense')),
                    array_sum(array_column($periods, 'general_expense'))
                ),
                'periods' => $rows,
                'expense_categories' => $expenseCategories,
                'inventory_purchases' => InventoryPurchaseResource::collection($purchases)->resolve(),
            ];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * Remove a wrongly entered stock purchase from the expenses. The item's stock quantity is not touched.
     *
     * @throws Exception
     */
    public function destroyPurchase(InventoryPurchase $inventoryPurchase): void
    {
        try {
            $inventoryPurchase->delete();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    private function amounts(float $income, float $inventoryExpense, float $generalExpense): array
    {
        $values = [
            'income' => $income,
            'inventory_expense' => $inventoryExpense,
            'general_expense' => $generalExpense,
            'total_expense' => $inventoryExpense + $generalExpense,
            'net_profit' => $income - $inventoryExpense - $generalExpense,
        ];

        $amounts = [];
        foreach ($values as $key => $value) {
            $amounts[$key] = round($value, 6);
            // "-Rs500.00" rather than "Rs-500.00" for a loss.
            $amounts[$key . '_currency_price'] = ($value < 0 ? '-' : '') . AppLibrary::currencyAmountFormat(abs($value));
        }

        return $amounts;
    }
}
