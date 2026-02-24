<x-mail::message>
# How was your order?

Hi {{ $order->customer->name }},

We hope you're enjoying your recent purchase! We'd love to hear your feedback.

## Items from Order #{{ $order->id }}

@foreach($order->items as $item)
@if($item->item)
- **{{ $item->item->name ?? $item->name }}**
@endif
@endforeach

Your review helps other customers make informed decisions and helps us improve our products and services.

<x-mail::button :url="route('orders.show', $order)">
Leave a Review
</x-mail::button>

Thank you for your business!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
