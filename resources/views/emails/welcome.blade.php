<x-mail::message>
# Welcome, {{ $customer->name }}!

Thank you for creating an account with **{{ config('business.profile.name', config('app.name')) }}**.

We're excited to have you on board. Here's what you can do now:

- **Browse our catalog** of unique products and designs
- **Track your orders** and manage your account
- **Save your favorites** to your wishlist

<x-mail::button :url="route('products.index')">
Start Shopping
</x-mail::button>

If you have any questions, don't hesitate to reach out. We're here to help!

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
