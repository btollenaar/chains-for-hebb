<x-mail::message>
# You Left Something Behind!

Hi {{ $customer->name }},

We noticed you have items waiting in your cart. Don't miss out — they're still available!

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

Need help deciding? Our team is here to answer any questions.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
