@php
    use App\Enums\OrderType;
    use App\Enums\PosPaymentMethod;

    $orderTypes = [
        OrderType::DELIVERY     => 'Delivery',
        OrderType::TAKEAWAY     => 'Takeaway',
        OrderType::POS          => 'POS',
        OrderType::DINING_TABLE => 'Dining Table',
    ];
    $paymentMethods = [
        PosPaymentMethod::CASH           => 'Cash',
        PosPaymentMethod::CARD           => 'Card',
        PosPaymentMethod::MOBILE_BANKING => 'Mobile Banking',
        PosPaymentMethod::OTHER          => 'Other',
    ];

    $branch   = $invoice['branch'] ?? [];
    $user     = $invoice['user'] ?? [];
    // The seeded walk-in account is a placeholder, not a real customer name.
    $customer = ($user['username'] ?? null) === 'default-customer' ? '' : ($user['name'] ?? '');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice['order_serial_no'] }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 14px 12px;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8px;
            color: #1F1F39;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 2px 0;
            vertical-align: top;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .muted {
            font-size: 7px;
        }

        .dashed-bottom {
            border-bottom: 1px dashed #9CA3AF;
        }

        .dashed-top {
            border-top: 1px dashed #9CA3AF;
        }

        .header {
            padding-bottom: 8px;
        }

        .company {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 3px;
        }

        .header p {
            margin: 0;
        }

        .section {
            padding: 5px 0;
        }

        .qty {
            width: 18px;
        }

        .token {
            font-size: 13px;
            font-weight: bold;
            padding: 6px 0;
        }
    </style>
</head>

<body>
    <div class="header text-center dashed-bottom">
        @if (!empty($logo))
            <img src="{{ $logo['src'] }}" width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                style="width: {{ $logo['width'] }}pt; height: {{ $logo['height'] }}pt; margin-bottom: 4px;" alt="">
        @endif
        <p class="company">{{ $company['company_name'] ?? '' }}</p>
        @if (!empty($branch['address']))
            <p>{{ $branch['address'] }}</p>
        @endif
        @if (!empty($branch['phone']))
            <p>Tel: {{ $branch['phone'] }}</p>
        @endif
    </div>

    <table class="section">
        <tr>
            <td class="bold">Invoice #{{ $invoice['order_serial_no'] }}</td>
        </tr>
        <tr>
            <td class="bold">{{ $invoice['order_date'] }}</td>
            <td class="bold text-right">{{ $invoice['order_time'] }}</td>
        </tr>
        @if ($customer)
            <tr>
                <td class="bold" colspan="2">Customer: {{ $customer }}</td>
            </tr>
        @endif
    </table>

    <table>
        <thead>
            <tr class="dashed-top dashed-bottom">
                <th class="qty">Qty</th>
                <th>Item</th>
                <th class="text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice['order_items'] as $item)
                @php
                    $variations = array_filter($item['item_variations'] ?? [], fn($v) => !empty($v['variation_name']) && !empty($v['name']));
                    $extras     = array_filter($item['item_extras'] ?? [], fn($e) => !empty($e['name']));
                @endphp
                <tr>
                    <td class="qty">{{ $item['quantity'] }}</td>
                    <td>
                        {{ $item['item_name'] }}
                        @if ($variations)
                            <div class="muted">
                                {{ implode(', ', array_map(fn($v) => $v['variation_name'] . ': ' . $v['name'], $variations)) }}
                            </div>
                        @endif
                        @if ($extras)
                            <div class="muted">Extras: {{ implode(', ', array_column($extras, 'name')) }}</div>
                        @endif
                        @if (!empty($item['instruction']))
                            <div class="muted">Instruction: {{ $item['instruction'] }}</div>
                        @endif
                        @if ($item['tax_rate'] > 0)
                            <div class="muted">
                                {{ $item['tax_name'] }} ({{ $item['tax_currency_rate'] }}{{ $item['tax_type'] === '%' ? '%' : '' }}):
                                {{ $item['tax_currency_amount'] }}
                            </div>
                        @endif
                    </td>
                    <td class="text-right">{{ $item['total_without_tax_currency_price'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="section dashed-top">
        <tr>
            <td>SUBTOTAL:</td>
            <td class="text-right">{{ $invoice['subtotal_without_tax_currency_price'] }}</td>
        </tr>
        <tr>
            <td>TOTAL TAX:</td>
            <td class="text-right">{{ $invoice['total_tax_currency_price'] }}</td>
        </tr>
        <tr>
            <td>DISCOUNT:</td>
            <td class="text-right">{{ $invoice['discount_currency_price'] }}</td>
        </tr>
        @if ($invoice['order_type'] == OrderType::DELIVERY)
            <tr>
                <td>DELIVERY CHARGE:</td>
                <td class="text-right">{{ $invoice['delivery_charge_currency_price'] }}</td>
            </tr>
        @endif
        <tr>
            <td class="bold">TOTAL:</td>
            <td class="bold text-right">{{ $invoice['total_currency_price'] }}</td>
        </tr>
    </table>

    <table class="section dashed-top dashed-bottom">
        <tr>
            <td colspan="2">Order Type: {{ $orderTypes[$invoice['order_type']] ?? '' }}</td>
        </tr>
        <tr>
            <td>Payment Type: {{ $paymentMethods[$invoice['pos_payment_method']] ?? '' }}</td>
            @if ($invoice['cash_back_amount'] > 0)
                <td class="text-right">
                    Cash: {{ $invoice['pos_received_currency_amount'] }}<br>
                    Change: {{ $invoice['cash_back_currency_amount'] }}
                </td>
            @endif
        </tr>
    </table>

    @if (!empty($invoice['token']))
        <div class="token text-center dashed-bottom">Token #{{ $invoice['token'] }}</div>
    @endif

    <div class="section text-center">
        <p style="margin: 0;">Thank you</p>
        <p style="margin: 0;">Please come again</p>
    </div>
</body>

</html>
