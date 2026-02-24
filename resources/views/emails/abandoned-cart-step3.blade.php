<x-mail::message>
# Last Chance: 5% Off Your Cart

Hi {{ $customer->name }},

Your items are still waiting — and we'd love to help you take the leap. Here's a little something to make it easier:

<x-mail::panel>
Use code **CART5** at checkout for **5% off** your order.
</x-mail::panel>

## Your Cart Items

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($cartItems as $cartItem)
| {{ $cartItem->item->name ?? 'Item' }} | {{ $cartItem->quantity }} | ${{ number_format(($cartItem->item->current_price ?? $cartItem->item->base_price ?? $cartItem->item->price ?? 0) * $cartItem->quantity, 2) }} |
@endforeach
| | **Total** | **${{ number_format($cartTotal, 2) }}** |
</x-mail::table>

<x-mail::button :url="route('cart.index') . '?promo=CART5'">
Complete Your Order — 5% Off
</x-mail::button>

This offer won't last forever — grab your items before they're gone.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
