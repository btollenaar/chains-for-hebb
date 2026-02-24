<x-mail::message>
# You Left Something Behind!

Hi {{ $customer->name }},

We noticed you have items waiting in your cart. They're still available — here's a quick look at what you picked out:

## Your Cart Items

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($cartItems as $cartItem)
| {{ $cartItem->item->name ?? 'Item' }} | {{ $cartItem->quantity }} | ${{ number_format(($cartItem->item->current_price ?? $cartItem->item->base_price ?? $cartItem->item->price ?? 0) * $cartItem->quantity, 2) }} |
@endforeach
| | **Total** | **${{ number_format($cartTotal, 2) }}** |
</x-mail::table>

<x-mail::button :url="route('cart.index')">
Complete Your Order
</x-mail::button>

No pressure — your items will be here when you're ready.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
