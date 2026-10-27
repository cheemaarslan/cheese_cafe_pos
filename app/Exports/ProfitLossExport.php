<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Profit & Loss report as a workbook: summary, the day/month breakdown, expenses by category and stock purchases.
 * Amounts are plain numbers so they can be added up in Excel.
 */
class ProfitLossExport implements WithMultipleSheets
{
    public function __construct(private array $report)
    {
    }

    public function sheets(): array
    {
        $report = $this->report;
        $summary = $report['summary'];
        $amount = fn ($value) => round((float) $value, 2);

        $summaryRows = [
            [trans('all.label.period'), $report['converted_from_date'] . ' - ' . $report['converted_to_date']],
            [trans('all.label.filters'), $report['filters'] ? implode(', ', $report['filters']) : trans('all.label.all_orders')],
            [trans('all.label.orders'), $summary['orders']],
            [trans('all.label.income'), $amount($summary['income'])],
            [trans('all.label.inventory_purchases'), $amount($summary['inventory_expense'])],
            [trans('all.label.other_expenses'), $amount($summary['general_expense'])],
            [trans('all.label.total_expenses'), $amount($summary['total_expense'])],
            [trans('all.label.net_profit'), $amount($summary['net_profit'])],
        ];

        $breakdownRows = array_map(fn ($period) => [
            $period['label'], $period['orders'], $amount($period['income']), $amount($period['inventory_expense']),
            $amount($period['general_expense']), $amount($period['net_profit']),
        ], $report['periods']);
        $breakdownRows[] = [
            trans('all.label.total'), $summary['orders'], $amount($summary['income']), $amount($summary['inventory_expense']),
            $amount($summary['general_expense']), $amount($summary['net_profit']),
        ];

        $categoryRows = array_map(fn ($category) => [
            $category['category'] !== '' ? $category['category'] : trans('all.label.uncategorized'), $amount($category['amount']),
        ], $report['expense_categories']);
        $categoryRows[] = [trans('all.label.total'), $amount($summary['general_expense'])];

        $purchaseRows = array_map(fn ($purchase) => [
            $purchase['converted_purchase_date'], $purchase['item_name'], (float) $purchase['quantity'], $purchase['unit'],
            $amount($purchase['unit_cost']), $amount($purchase['total_cost']),
        ], $report['inventory_purchases']);
        $purchaseRows[] = [trans('all.label.total'), '', '', '', '', $amount($summary['inventory_expense'])];

        return [
            new ProfitLossSheetExport(trans('all.label.summary'), [trans('all.label.profit_loss'), ''], $summaryRows),
            new ProfitLossSheetExport(
                trans($report['group_by'] === 'month' ? 'all.label.monthly_breakdown' : 'all.label.daily_breakdown'),
                [
                    trans('all.label.date'), trans('all.label.orders'), trans('all.label.income'),
                    trans('all.label.inventory_purchases'), trans('all.label.other_expenses'), trans('all.label.net_profit'),
                ],
                $breakdownRows
            ),
            new ProfitLossSheetExport(trans('all.label.expenses_by_category'), [trans('all.label.category'), trans('all.label.amount')], $categoryRows),
            new ProfitLossSheetExport(
                trans('all.label.inventory_purchases'),
                [
                    trans('all.label.date'), trans('all.label.item'), trans('all.label.quantity'), trans('all.label.unit'),
                    trans('all.label.unit_cost'), trans('all.label.total'),
                ],
                $purchaseRows
            ),
        ];
    }
}
