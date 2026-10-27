<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('all.label.profit_loss') }}</title>
    <style>
        @page {
            margin: 28px 28px 48px 28px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #1F1F39;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
        }

        .header p {
            margin: 0 0 4px 0;
        }

        .company {
            font-size: 15px;
            font-weight: bold;
        }

        .title {
            color: #b7161d;
            font-size: 15px;
            font-weight: bold;
            margin-top: 6px !important;
        }

        .muted {
            color: #6E7191;
        }

        h3 {
            font-size: 12px;
            margin: 18px 0 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #EFF0F6;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #F8FBFB;
            font-weight: bold;
        }

        td {
            color: #4E4B66;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .total td {
            color: #1F1F39;
            font-weight: bold;
            background-color: #F7F7FC;
        }

        .profit,
        .total td.profit {
            color: #1AB759;
        }

        .loss,
        .total td.loss {
            color: #FB4E4E;
        }

        .summary td {
            width: 50%;
        }

        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #6E7191;
        }
    </style>
</head>

<body>
    @php
        $money = fn ($value) => App\Libraries\AppLibrary::reportCurrencyAmountFormat($value);
        $summary = $report['summary'];
    @endphp

    <div class="footer">{{ $copyright }}</div>

    <div class="header">
        <p class="company">{{ App\Libraries\AppLibrary::textShortener($company['company_name'] ?? '', 60) }}</p>
        <p class="muted">{{ App\Libraries\AppLibrary::textShortener($company['company_address'] ?? '', 80) }}</p>
        <p class="title">{{ trans('all.label.profit_loss') }}</p>
        <p>{{ $report['converted_from_date'] }} - {{ $report['converted_to_date'] }}</p>
        <p class="muted">
            {{ trans('all.label.filters') }}:
            {{ $report['filters'] ? implode(', ', $report['filters']) : trans('all.label.all_orders') }}
        </p>
    </div>

    <h3>{{ trans('all.label.summary') }}</h3>
    <table class="summary">
        <tbody>
            <tr>
                <td>{{ trans('all.label.orders') }}</td>
                <td class="number">{{ $summary['orders'] }}</td>
            </tr>
            <tr>
                <td>{{ trans('all.label.income') }}</td>
                <td class="number">{{ $money($summary['income']) }}</td>
            </tr>
            <tr>
                <td>{{ trans('all.label.inventory_purchases') }}</td>
                <td class="number">{{ $money($summary['inventory_expense']) }}</td>
            </tr>
            <tr>
                <td>{{ trans('all.label.other_expenses') }}</td>
                <td class="number">{{ $money($summary['general_expense']) }}</td>
            </tr>
            <tr>
                <td>{{ trans('all.label.total_expenses') }}</td>
                <td class="number">{{ $money($summary['total_expense']) }}</td>
            </tr>
            <tr class="total">
                <td>{{ trans('all.label.net_profit') }}</td>
                <td class="number {{ $summary['net_profit'] < 0 ? 'loss' : 'profit' }}">{{ $money($summary['net_profit']) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>{{ trans($report['group_by'] === 'month' ? 'all.label.monthly_breakdown' : 'all.label.daily_breakdown') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ trans('all.label.date') }}</th>
                <th class="number">{{ trans('all.label.orders') }}</th>
                <th class="number">{{ trans('all.label.income') }}</th>
                <th class="number">{{ trans('all.label.inventory_purchases') }}</th>
                <th class="number">{{ trans('all.label.other_expenses') }}</th>
                <th class="number">{{ trans('all.label.net_profit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['periods'] as $period)
                <tr>
                    <td>{{ $period['label'] }}</td>
                    <td class="number">{{ $period['orders'] }}</td>
                    <td class="number">{{ $money($period['income']) }}</td>
                    <td class="number">{{ $money($period['inventory_expense']) }}</td>
                    <td class="number">{{ $money($period['general_expense']) }}</td>
                    <td class="number {{ $period['net_profit'] < 0 ? 'loss' : 'profit' }}">{{ $money($period['net_profit']) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>{{ trans('all.label.total') }}</td>
                <td class="number">{{ $summary['orders'] }}</td>
                <td class="number">{{ $money($summary['income']) }}</td>
                <td class="number">{{ $money($summary['inventory_expense']) }}</td>
                <td class="number">{{ $money($summary['general_expense']) }}</td>
                <td class="number {{ $summary['net_profit'] < 0 ? 'loss' : 'profit' }}">{{ $money($summary['net_profit']) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>{{ trans('all.label.expenses_by_category') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ trans('all.label.category') }}</th>
                <th class="number">{{ trans('all.label.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['expense_categories'] as $category)
                <tr>
                    <td>{{ $category['category'] !== '' ? $category['category'] : trans('all.label.uncategorized') }}</td>
                    <td class="number">{{ $money($category['amount']) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td>{{ trans('all.label.total') }}</td>
                <td class="number">{{ $money($summary['general_expense']) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>{{ trans('all.label.inventory_purchases') }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ trans('all.label.date') }}</th>
                <th>{{ trans('all.label.item') }}</th>
                <th class="number">{{ trans('all.label.quantity') }}</th>
                <th class="number">{{ trans('all.label.unit_cost') }}</th>
                <th class="number">{{ trans('all.label.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['inventory_purchases'] as $purchase)
                <tr>
                    <td>{{ $purchase['converted_purchase_date'] }}</td>
                    <td>{{ $purchase['item_name'] }}</td>
                    <td class="number">{{ rtrim(rtrim(number_format((float) $purchase['quantity'], 3, '.', ','), '0'), '.') }} {{ $purchase['unit'] }}</td>
                    <td class="number">{{ $money($purchase['unit_cost']) }}</td>
                    <td class="number">{{ $money($purchase['total_cost']) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="4">{{ trans('all.label.total') }}</td>
                <td class="number">{{ $money($summary['inventory_expense']) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
