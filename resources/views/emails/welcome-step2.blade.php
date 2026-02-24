<x-mail::message>
# What We're All About

Hi {{ $customer->name }},

We started **{{ config('business.profile.name', config('app.name')) }}** because we believe in the power of great design. Whether it's a bold statement piece or a subtle everyday item, the things you surround yourself with should reflect who you are.

## Our Values

- **Design first** — Every product starts with original artwork and creative vision. We put care into every detail.
- **Quality matters** — We use premium materials and trusted manufacturing partners so your order looks and feels great.
- **Customer-focused** — From easy ordering to fast shipping, we want every experience with us to be a good one.
- **Always growing** — We're constantly adding new designs and products. There's always something fresh to discover.

## What Sets Us Apart

We handle the details so you don't have to. Every order is printed on demand, carefully packed, and shipped directly to you. No mass production, no warehouses full of unsold inventory — just the products you want, made when you order them.

<x-mail::button :url="route('products.index')">
Explore Our Collections
</x-mail::button>

Thanks for being a part of our community,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
