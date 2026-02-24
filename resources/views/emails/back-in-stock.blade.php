<x-mail::message>
# Great News!

The product you've been waiting for is back in stock!

**{{ $product->name }}**

@if($product->isOnSale)
~~${{ number_format($product->price, 2) }}~~ **${{ number_format($product->currentPrice, 2) }}**
@else
**${{ number_format($product->price, 2) }}**
@endif

Don't wait too long -- popular items sell out quickly!

<x-mail::button :url="route('products.show', $product->slug)">
Shop Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
