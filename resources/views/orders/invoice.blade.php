<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('message.Invoice') }} #{{ $order->id }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction:
                {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}
            ;
            text-align:
                {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            ;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
        }

        .header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #222;
            margin-bottom: 3px;
        }

        .header h4 {
            font-size: 14px;
            color: #666;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 11px;
            color: #888;
        }

        .info-grid {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-section {
            flex: 1;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
        }

        .info-section h4 {
            font-size: 13px;
            margin-bottom: 6px;
            color: #444;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .info-item {
            margin-bottom: 2px;
            font-size: 11px;
        }

        .info-item strong {
            font-weight: 600;
            color: #222;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: 600;
            font-size: 11px;
            color: #222;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .products-table td:first-child {
            width: 30px;
            text-align: center;
        }

        .products-table td:nth-child(3),
        .products-table td:nth-child(4),
        .products-table td:nth-child(5) {
            width: 60px;
            text-align: center;
        }

        .attributes {
            font-size: 10px;
            color: #666;
        }

        .attributes div {
            margin-bottom: 1px;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .totals {
            width: 300px;
            border: 1px solid #ddd;
        }

        .totals td {
            padding: 4px 8px;
            font-size: 11px;
        }

        .totals tr:last-child td {
            font-weight: 700;
            background-color: #f0f0f0;
            font-size: 12px;
        }

        .totals td:first-child {
            text-align:
                {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            ;
            width: 60%;
        }

        .totals td:last-child {
            text-align:
                {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}
            ;
            font-weight: 600;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin: 10px 0 5px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }

        /* Print optimizations */
        @media print {
            body {
                padding: 10px;
            }

            .header {
                margin-bottom: 10px;
            }

            .info-grid {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ __('message.Order Invoice') }}</h2>
        <h4>#{{ $order->id }}</h4>
        <p>{{ $order->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h4>{{ __('message.Customer Info') }}</h4>
            <div class="info-item"><strong>{{ __('message.Name') }}:</strong> {{ $order->user?->name ?? '-' }}</div>
            <div class="info-item"><strong>{{ __('message.Phone') }}:</strong> {{ $order->user?->phone ?? '-' }}</div>
            <div class="info-item"><strong>{{ __('message.Address') }}:</strong> {{ $order->address?->address ?? '-' }}
            </div>
            <div class="info-item"><strong>{{ __('message.City') }}:</strong> {{ (app()->getLocale() === 'ar' ? $order->address?->city?->name_ar : $order->address?->city?->name_en )?? '-' }}
            </div>
            <div class="info-item"><strong>{{ __('message.Area') }}:</strong> {{ (app()->getLocale() === 'ar' ? $order->area?->name_ar : $order->area?->name_en) ?? '-' }}</div>
            @if($order->deliveryman)
                <div class="info-item"><strong>{{ __('message.Deliveryman') }}:</strong> {{ $order->deliveryman?->name }} -
                    {{ $order->deliveryman?->phone }}</div>
            @endif
        </div>

        <div class="info-section">
            <h4>{{ __('message.Order Info') }}</h4>
            <div class="info-item"><strong>{{ __('message.Payment Method') }}:</strong>
                {{ __("message." . ucfirst($order->payment_method)) }}</div>
            <div class="info-item"><strong>{{ __('message.Payment Status') }}:</strong>
                {{ __("message." . ucfirst($order->payment_status)) }}</div>
            <div class="info-item"><strong>{{ __('message.Order Type') }}:</strong>
                {{ __("message." . ucfirst($order->order_type)) }}</div>
            <div class="info-item"><strong>{{ __('message.Status') }}:</strong> {{ __("message." . $order->status) }}
            </div>
            @if($order->coupon)
                <div class="info-item"><strong>{{ __('message.Coupon Code') }}:</strong> {{ $order->coupon->code }}</div>
                <div class="info-item"><strong>{{ __('message.Discount Type') }}:</strong>
                    {{ __('message.' . ($order->coupon->discount_type->value ?? '-')) }}</div>
            @endif
            @if($order->notes)
                <div class="info-item"><strong>{{ __('message.Notes') }}:</strong> {{ $order->notes }}</div>
            @endif
        </div>
    </div>

    <div class="section-title">{{ __('message.Products') }}</div>
    <table class="products-table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('message.Product') }}</th>
                <th>{{ __('message.Quantity') }}</th>
                <th>{{ __('message.Price') }}</th>
                <th>{{ __('message.Total') }}</th>
                <th>{{ __('message.Attributes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $i => $detail)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ app()->getLocale() === 'ar' ? $detail->product?->name_ar : $detail->product?->name_en }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ number_format($detail->price, 2) }}</td>
                    <td>{{ number_format($detail->price * $detail->quantity, 2) }}</td>
                    <td class="attributes">
                        @forelse($detail->attributeValues as $attr)
                            <div>
                                {{ $attr->attributeValue?->attribute?->{app()->getLocale() === 'ar' ? 'name_ar' : 'name_en'} ?? '' }}:
                                {{ $attr->attributeValue?->value ?? '' }}
                            </div>
                        @empty
                            -
                        @endforelse
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <table class="totals">
            <tr>
                <td>{{ __('message.Subtotal') }}</td>
                <td>{{ number_format($order->subtotal, 2) }} {{ __("message.Currency") }}</td>
            </tr>
            <tr>
                <td>{{ __('message.Discount') }}</td>
                <td>{{ number_format($order->discount, 2) }} {{ __("message.Currency") }}</td>
            </tr>
            <tr>
                <td>{{ __('message.Delivery Fee') }}</td>
                <td>{{ number_format($order->delivery_fee, 2) }} {{ __("message.Currency") }}</td>
            </tr>
            <tr>
                <td>{{ __('message.Total') }}</td>
                <td>{{ number_format($order->total, 2) }} {{ __("message.Currency") }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        {{ __('message.Thank you for your purchase!') }}
    </div>
</body>

</html>