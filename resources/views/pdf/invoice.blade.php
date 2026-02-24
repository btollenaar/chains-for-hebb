<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .text-right { text-align: right; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items thead th { background: #6B5F4A; color: white; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 11px; }
        table.items tbody tr:nth-child(even) { background: #fafaf8; }
    </style>
</head>
<body>
    {{-- Header --}}
    <table style="width: 100%; margin-bottom: 30px; border-bottom: 2px solid #6B5F4A; padding-bottom: 20px;">
        <tr>
            <td style="vertical-align: top;">
                <h1 style="font-size: 24px; color: #6B5F4A; margin-bottom: 5px;">{{ $businessName }}</h1>
                @if(is_array($businessAddress) && !empty($businessAddress))
                    <p style="font-size: 10px; color: #666;">
                        {{ $businessAddress['street'] ?? '' }}<br>
                        {{ $businessAddress['city'] ?? '' }}, {{ $businessAddress['state'] ?? '' }} {{ $businessAddress['zip'] ?? '' }}
                    </p>
                @endif
                <p style="font-size: 10px; color: #666;">{{ $businessEmail }}@if($businessPhone) | {{ $businessPhone }}@endif</p>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <h2 style="font-size: 20px; color: #2D6069; margin-bottom: 10px;">INVOICE</h2>
                <p style="font-size: 11px; color: #666;">
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('F j, Y') }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->payment_status) }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Addresses --}}
    <table style="width: 100%; margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <h3 style="font-size: 11px; text-transform: uppercase; color: #6B5F4A; margin-bottom: 8px; letter-spacing: 1px;">Bill To</h3>
                <p style="font-size: 11px; color: #555;">
                    {{ $order->customer->name }}<br>
                    {{ $order->customer->email }}<br>
                    @if($order->billing_address)
                        {{ $order->billing_address['street'] ?? '' }}<br>
                        {{ $order->billing_address['city'] ?? '' }}, {{ $order->billing_address['state'] ?? '' }} {{ $order->billing_address['zip'] ?? '' }}
                    @endif
                </p>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <h3 style="font-size: 11px; text-transform: uppercase; color: #6B5F4A; margin-bottom: 8px; letter-spacing: 1px;">Ship To</h3>
                <p style="font-size: 11px; color: #555;">
                    @if($order->shipping_address)
                        {{ $order->shipping_address['street'] ?? '' }}<br>
                        {{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['zip'] ?? '' }}
                    @endif
                </p>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Unit Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->name ?? ($item->item->name ?? 'N/A') }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">${{ number_format($item->unit_price ?? 0, 2) }}</td>
                <td style="text-align: right;">${{ number_format($item->total ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table style="width: 300px; margin-left: auto; margin-top: 20px;">
        <tr>
            <td style="padding: 6px 12px; font-size: 11px;">Subtotal</td>
            <td style="padding: 6px 12px; font-size: 11px; text-align: right;">${{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->coupon_code)
        <tr>
            <td style="padding: 6px 12px; font-size: 11px;">Discount ({{ $order->coupon_code }})</td>
            <td style="padding: 6px 12px; font-size: 11px; text-align: right; color: #10B981;">-${{ number_format($order->discount_amount ?? 0, 2) }}</td>
        </tr>
        @endif
        @if($order->shipping_cost > 0)
        <tr>
            <td style="padding: 6px 12px; font-size: 11px;">Shipping ({{ ucfirst($order->shipping_method ?? 'Standard') }})</td>
            <td style="padding: 6px 12px; font-size: 11px; text-align: right;">${{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 6px 12px; font-size: 11px;">Tax</td>
            <td style="padding: 6px 12px; font-size: 11px; text-align: right;">${{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        <tr style="border-top: 2px solid #6B5F4A;">
            <td style="padding: 10px 12px; font-weight: bold; font-size: 14px; color: #2D6069;">Total</td>
            <td style="padding: 10px 12px; font-weight: bold; font-size: 14px; color: #2D6069; text-align: right;">${{ number_format($order->total_amount, 2) }}</td>
        </tr>
    </table>

    {{-- Payment Info --}}
    <div style="margin-top: 30px; padding: 15px; background: #f8f7f4; border-radius: 4px;">
        <h3 style="font-size: 11px; text-transform: uppercase; color: #6B5F4A; margin-bottom: 8px;">Payment Information</h3>
        <p style="font-size: 11px; color: #555;">
            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}<br>
            <strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}
            @if($order->shipped_at)
                <br><strong>Shipped On:</strong> {{ $order->shipped_at->format('F j, Y') }}
            @endif
            @if($order->delivered_at)
                <br><strong>Delivered On:</strong> {{ $order->delivered_at->format('F j, Y') }}
            @endif
        </p>
    </div>

    {{-- Footer --}}
    <div style="margin-top: 40px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999;">
        <p>Thank you for your purchase! | {{ $businessName }} | {{ $businessEmail }}</p>
    </div>
</body>
</html>
