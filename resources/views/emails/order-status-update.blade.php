<x-mail::message>
@if($newStatus === 'shipped')
# Your Order Has Shipped!

Great news! Your order **#{{ $order->order_number }}** has been shipped and is on its way to you.

@if($order->tracking_number)
## Tracking Information

**Carrier:** {{ strtoupper($order->tracking_carrier ?? 'N/A') }}
**Tracking Number:** {{ $order->tracking_number }}

@if($order->tracking_url)
<x-mail::button :url="$order->tracking_url">
Track Your Package
</x-mail::button>
@endif
@endif

@elseif($newStatus === 'delivered')
# Your Order Has Been Delivered!

Your order **#{{ $order->order_number }}** has been delivered. We hope you enjoy your purchase!

@endif

## Order Summary

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | ${{ number_format($item->total, 2) }} |
@endforeach
| **Total** | | **${{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

@if($newStatus === 'delivered')
If you have a moment, we'd love to hear your feedback on your purchase.

<x-mail::button :url="route('orders.show', $order)">
View Your Order
</x-mail::button>
@endif

## Shipping Address

{{ $order->shipping_address['street'] }}
{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['zip'] }}

If you have any questions about your order, please don't hesitate to contact us.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
