@extends('layouts.admin')

@section('title', 'Printful Catalog')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Printful Catalog</h1>
            <p class="text-sm text-gray-600 mt-1">Browse Printful's product catalog and add items to your store</p>
            @if($lastSyncTime)
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-sync-alt mr-1"></i>Last synced {{ \Carbon\Carbon::parse($lastSyncTime)->diffForHumans() }}
                </p>
            @endif
        </div>
        <div class="flex gap-3 flex-wrap">
            <form action="{{ route('admin.printful.sync-catalog') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-admin-secondary">
                    <i class="fas fa-sync-alt mr-2"></i>Sync Catalog
                </button>
            </form>
            <a href="{{ route('admin.products.index') }}" class="btn-admin-secondary">
                <i class="fas fa-box mr-2"></i>My Products
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-6">
        <form method="GET" action="{{ route('admin.printful.catalog') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search products..."
                    class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
            </div>
            <div class="w-full md:w-48">
                <select name="category" class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-admin-primary btn-admin-sm">
                    <i class="fas fa-search mr-1"></i>Filter
                </button>
                <a href="{{ route('admin.printful.catalog') }}" class="btn-admin-secondary btn-admin-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- Results Count --}}
    <p class="text-sm text-gray-500 mb-4">{{ $products->total() }} products found</p>

    {{-- Product Grid --}}
    @if($products->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-database text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No catalog products found</h3>
            <p class="text-gray-500 mb-4">Click "Sync Catalog" to fetch products from Printful.</p>
            <form action="{{ route('admin.printful.sync-catalog') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-sync-alt mr-2"></i>Sync Catalog Now
                </button>
            </form>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                    {{-- Product Image --}}
                    <div class="aspect-square bg-gray-100 relative">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-4xl"></i>
                            </div>
                        @endif

                        {{-- Already Added Badge --}}
                        @if(in_array($product->printful_product_id, $existingPrintfulIds))
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                                <i class="fas fa-check mr-1"></i>In Store
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 text-sm line-clamp-2 mb-1">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mb-2">{{ $product->category ?? 'Uncategorized' }}</p>

                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="text-sm font-semibold text-gray-900">{{ $product->price_range }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $product->variant_count }} variant{{ $product->variant_count !== 1 ? 's' : '' }}
                            </div>
                        </div>

                        {{-- Color/Size Summary --}}
                        @if($product->colors_json && count($product->colors_json) > 0)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($product->colors_json, 0, 6) as $colorName => $colorHex)
                                    <span class="w-4 h-4 rounded-full border border-gray-200" title="{{ $colorName }}"
                                        style="background-color: {{ $colorHex ?: '#ccc' }}"></span>
                                @endforeach
                                @if(count($product->colors_json) > 6)
                                    <span class="text-xs text-gray-400">+{{ count($product->colors_json) - 6 }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Action --}}
                        @if(in_array($product->printful_product_id, $existingPrintfulIds))
                            <span class="inline-block w-full text-center text-sm text-gray-400 py-2">Already added</span>
                        @else
                            <a href="{{ route('admin.printful.setup', $product->printful_product_id) }}"
                                class="btn-admin-primary btn-admin-sm w-full text-center block">
                                <i class="fas fa-plus mr-1"></i>Add to Store
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
