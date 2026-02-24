<x-mail::message>
# Order Confirmation

Thank you for your order, {{ $order->customer->name }}!

Your order has been received and is being processed.

## Order Details

**Order Number:** #{{ $order->id }}
**Order Date:** {{ $order->created_at->format('F j, Y') }}
**Payment Status:** {{ ucfirst($order->payment_status) }}

## Items Ordered

<x-mail::table>
| Item | Quantity | Price | Total |
|:-----|:--------:|------:|------:|
@foreach($order->items as $item)
| {{ $item->snapshot['name'] ?? 'Item' }} | {{ $item->quantity }} | ${{ number_format($item->snapshot['price'] ?? 0, 2) }} | ${{ number_format(($item->snapshot['price'] ?? 0) * $item->quantity, 2) }} |
@endforeach
</x-mail::table>

## Order Summary

**Subtotal:** ${{ number_format($order->subtotal, 2) }}
@if($order->tax_amount > 0)
**Tax:** ${{ number_format($order->tax_amount, 2) }}
@endif
@if($order->discount_amount > 0)
**Discount:** -${{ number_format($order->discount_amount, 2) }}
@endif
**Total:** ${{ number_format($order->total_amount, 2) }}

## Shipping Address

{{ $order->shipping_address['street'] }}
{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['zip'] }}

@if($order->notes)
## Order Notes

{{ $order->notes }}
@endif

We'll send you another email when your order ships.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
