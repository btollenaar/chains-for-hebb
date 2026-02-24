<x-mail::message>
# Welcome, {{ $customer->name }}!

Thank you for joining **{{ config('business.profile.name', config('app.name')) }}**! We're excited to have you.

To get you started, here's a special welcome gift:

---

## Your Exclusive Discount

Use code **WELCOME10** at checkout for **10% off** your first order.

<x-mail::panel>
**WELCOME10** — 10% off your first order
</x-mail::panel>

---

Here's what you'll find:

- **Unique designs** — Original artwork and creative prints you won't find anywhere else
- **Quality materials** — Premium products made to look great and last
- **Fast fulfillment** — Printed and shipped directly to your door
- **Easy returns** — Love it or send it back, hassle-free

<x-mail::button :url="route('products.index') . '?promo=WELCOME10'">
Start Shopping — 10% Off
</x-mail::button>

We can't wait for you to find something you love.

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
