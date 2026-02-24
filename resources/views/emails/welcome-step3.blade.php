<x-mail::message>
# 5 Reasons to Love Your New Favorite Store

Hi {{ $customer->name }},

Still exploring? Here are five things our customers love about shopping with us — and we think you will too.

---

### 1. Original Designs

Every product features unique artwork you won't find in big-box stores. Stand out from the crowd with prints that have personality.

### 2. Premium Quality

We partner with top-tier manufacturers to ensure every item meets our standards. From the materials to the print quality, we don't cut corners.

### 3. Made Just for You

Every order is printed on demand — that means your product is made fresh when you place your order. No sitting in a warehouse for months.

### 4. Fast, Reliable Shipping

We know you're excited to get your order. Our fulfillment partners work quickly to get your items printed, packed, and on their way.

### 5. Easy Returns

Not 100% happy? No problem. We stand behind our products and make returns simple.

---

We're always adding new designs and products, so there's always something new to discover.

<x-mail::button :url="route('products.index')">
Browse Our Collection
</x-mail::button>

Happy shopping,<br>
{{ config('business.profile.name', config('app.name')) }}
</x-mail::message>
