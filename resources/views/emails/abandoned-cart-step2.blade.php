<x-mail::message>
# Still Thinking About It?

Hi {{ $customer->name }},

We get it — it's worth taking a moment to decide. Here's a reminder of what caught your eye:

## Your Cart Items

<x-mail::table>
| Item | Qty | Price |
|:-----|:---:|------:|
@foreach($cartItems as $cartItem)
| {{ $cartItem->item->name ?? 'Item' }} | {{ $cartItem->quantity }} | ${{ number_format(($cartItem->item->current_price ?? $cartItem->item->base_price ?? $cartItem->item->price ?? 0) * $cartItem->quantity, 2) }} |
@endforeach
| | **Total** | **${{ number_format($cartTotal, 2) }}** |
</x-mail::table>

---

## Why Our Customers Love These Products

**Made with care.** Every item is printed on demand using premium materials — so you get a product that looks great and lasts.

**Unique designs.** Our products feature original artwork you won't find anywhere else. They make great gifts, too.

**Satisfaction guaranteed.** We stand behind every order with easy returns and responsive customer support.

---

## Join Thousands of Happy Customers

Our customers keep coming back for the quality, the designs, and the experience. We'd love for you to see why.

<x-mail::button :url="route('cart.index')">
Return to Your Cart
</x-mail::button>

Questions? Just reply to this email — we're happy to help.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
