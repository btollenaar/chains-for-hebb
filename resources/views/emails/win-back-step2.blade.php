<x-mail::message>
# Welcome Back Gift: 10% Off

Hi {{ $customer->name }},

We really do miss you — so we'd like to offer you a little something to make your return extra special.

<x-mail::panel>
Use code **WELCOME10** at checkout for **10% off** your next order.
</x-mail::panel>

## Why Customers Love Us

- **Unique designs** you won't find anywhere else
- **Free shipping** on orders over ${{ number_format(\App\Models\Setting::get('shipping.free_threshold', 75), 0) }}
- **Premium quality** on every product
- **30-day returns** — no questions asked

<x-mail::button :url="route('products.index') . '?promo=WELCOME10'">
Shop Now — 10% Off
</x-mail::button>

This is our way of saying we value you as a customer. We hope to see you again soon!

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
