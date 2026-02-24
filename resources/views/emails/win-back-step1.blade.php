<x-mail::message>
# We Miss You, {{ $customer->name }}!

It's been a while since your last visit, and we've been busy adding new products to our collection.

## What's New

We're always expanding our selection with fresh designs and new products. Here are a few reasons to come back:

- **New arrivals** — Fresh designs added regularly across all categories
- **Seasonal favorites** — Hand-picked products perfect for right now
- **Inspiration & ideas** — Check out our blog for the latest from our team

## Your Favorites May Be Waiting

The products you loved are still here — and we've added even more options in the same categories.

<x-mail::button :url="route('products.index')">
See What's New
</x-mail::button>

We'd love to have you back. Happy browsing!

Thanks,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
