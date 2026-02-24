<x-mail::message>
# Low Stock Alert

The following {{ $products->count() }} product(s) are running low on inventory and may need restocking:

<x-mail::table>
| Product | Current Stock | Threshold | Status |
|:--------|:------------:|:---------:|:------:|
@foreach($products as $product)
| {{ $product->name }} | **{{ $product->stock_quantity }}** | {{ $product->low_stock_threshold }} | {{ $product->stock_quantity <= 5 ? 'Critical' : 'Low' }} |
@endforeach
</x-mail::table>

<x-mail::button :url="route('admin.products.index', ['stock' => 'low'])">
View Low Stock Products
</x-mail::button>

This is an automated alert from your inventory management system.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
