<x-mail::message>
# How's Everything Going?

Hi {{ $order->customer->name }},

It's been a little while since your order **#{{ $order->order_number }}** was delivered, and we'd love to hear how you're enjoying your purchase!

## Your Order Included

<x-mail::table>
| Item |
|:-----|
@foreach($order->items as $item)
| {{ $item->snapshot['name'] ?? 'Item' }} |
@endforeach
</x-mail::table>

Your feedback helps other customers make informed decisions and helps us continue to improve.

<x-mail::button :url="route('orders.show', $order)">
Leave a Review
</x-mail::button>

Thanks for being a valued customer!

Best,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
