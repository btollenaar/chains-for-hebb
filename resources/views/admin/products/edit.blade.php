@extends('layouts.admin')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    {{-- Breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-admin-teal hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Products
        </a>
    </div>

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row gap-6 mb-8">
        <div class="w-full md:w-48 flex-shrink-0">
            @if($headerImageUrl)
                <img src="{{ $headerImageUrl }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-sm">
            @else
                <div class="w-full aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-image text-gray-300 text-4xl"></i>
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            @if($product->isPrintful)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-admin-teal/10 text-admin-teal mt-1">
                    Printful Product #{{ $product->printful_product_id }}
                </span>
            @endif
            <p class="text-sm text-gray-500 mt-1">
                SKU: {{ $product->sku }}
                @if($product->isPrintful && $product->variants->count() > 0)
                    &middot; {{ $product->variants->count() }} variant(s)
                @endif
            </p>
            @php $pendingNotifications = $product->stockNotifications()->pending()->count(); @endphp
            @if($pendingNotifications > 0)
                <div class="mt-2 inline-flex items-center px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-lg">
                    <i class="fas fa-bell mr-1.5"></i>{{ $pendingNotifications }} customer(s) waiting for restock notification
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Form wraps both columns --}}
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if($product->isPrintful)
            <input type="hidden" name="price" value="{{ $product->price }}">
            <input type="hidden" name="stock_quantity" value="{{ $product->stock_quantity }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- ===================== MAIN COLUMN ===================== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card 1: Product Details --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">1</span>
                        Product Details
                    </h2>

                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $product->name) }}"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                            <input type="text" name="slug" id="slug"
                                value="{{ old('slug', $product->slug) }}"
                                class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                            @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- SKU & Barcode --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                                <input type="text" name="sku" id="sku" required
                                    value="{{ old('sku', $product->sku) }}"
                                    class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                @error('sku') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                                <input type="text" name="barcode" id="barcode"
                                    value="{{ old('barcode', $product->barcode) }}"
                                    class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                @error('barcode') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Short Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="wysiwyg-editor w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">{{ old('description', $product->description) }}</textarea>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">Brief product description (max 1000 characters)</p>
                        </div>

                        {{-- Long Description --}}
                        <div>
                            <label for="long_description" class="block text-sm font-medium text-gray-700 mb-1">Detailed Description</label>
                            <textarea name="long_description" id="long_description" rows="6"
                                class="wysiwyg-editor w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">{{ old('long_description', $product->long_description) }}</textarea>
                            @error('long_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">Detailed product information (max 5000 characters)</p>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Product Images --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">2</span>
                        Product Images
                    </h2>

                    {{-- Existing Images --}}
                    @if($product->images && is_array($product->images) && count($product->images) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @foreach($product->images as $index => $imagePath)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Product image {{ $index + 1 }}"
                                            class="w-full h-32 object-cover rounded">
                                        <div class="absolute top-1 left-1 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                            {{ $index === 0 ? 'Primary' : 'Image ' . ($index + 1) }}
                                        </div>
                                        <div class="absolute bottom-1 left-1 right-1">
                                            <label class="flex items-center bg-red-600 bg-opacity-90 text-white text-xs px-2 py-1 rounded cursor-pointer hover:bg-opacity-100">
                                                <input type="checkbox" name="remove_images[]" value="{{ $imagePath }}" class="mr-1">
                                                Remove
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Check the box to remove images when you save</p>
                        </div>
                    @endif

                    {{-- Upload New Images --}}
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ ($product->images && count($product->images) > 0) ? 'Add More Images' : 'Upload Images' }}
                        </label>
                        <input type="file" name="images[]" id="images" accept="image/*" multiple
                            class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                        @error('images') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">
                            {{ ($product->images && count($product->images) > 0) ? 'Existing images will be kept unless you mark them for removal above.' : 'Upload up to 5 images (JPEG, PNG, GIF, WebP). Max 5MB per image.' }}
                        </p>
                        <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4"></div>
                    </div>
                </div>
            </div>

            {{-- ===================== SIDEBAR ===================== --}}
            <div class="space-y-6 lg:sticky lg:top-20 lg:self-start">

                {{-- Status & Visibility --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Status & Visibility</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active (Published)</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-400">Inactive products are hidden from customers</p>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="featured" id="featured" value="1"
                                {{ old('featured', $product->featured) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal mr-2">
                            <label for="featured" class="text-sm text-gray-700">Featured product</label>
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Categories <span class="text-red-500">*</span></h3>
                    @error('category_ids') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror
                    @error('primary_category_id') <p class="text-red-500 text-xs mb-2">{{ $message }}</p> @enderror

                    <x-admin.category-tree-checkbox
                        :categories="$allCategories"
                        :selectedIds="$selectedCategoryIds"
                        :primaryId="$primaryCategoryId"
                        name="category_ids"
                        primaryName="primary_category_id" />

                    <p class="mt-2 text-xs text-gray-400">
                        <a href="{{ route('admin.products.categories.index') }}" target="_blank" class="text-admin-teal hover:underline">
                            <i class="fas fa-folder mr-1"></i>Manage Categories
                        </a>
                    </p>
                </div>

                {{-- Tags --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Tags</h3>
                    <input type="text" name="tags" id="tags"
                        value="{{ old('tags', $product->tags_string ?? (is_array($product->tags) ? implode(', ', $product->tags) : '')) }}"
                        placeholder="organic, natural, vegan"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                    @error('tags') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Comma-separated tags for filtering</p>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Pricing</h3>

                    @if($product->isPrintful)
                        {{-- Printful: read-only summary --}}
                        @php
                            $minPrice = $product->variants->where('is_active', true)->min('retail_price');
                            $maxPrice = $product->variants->where('is_active', true)->max('retail_price');
                            $avgCost = $product->variants->where('is_active', true)->avg('printful_cost');
                        @endphp
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Price range</span>
                                <span class="font-medium">
                                    @if($minPrice && $maxPrice)
                                        ${{ number_format($minPrice, 2) }} — ${{ number_format($maxPrice, 2) }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Avg. cost</span>
                                <span class="font-medium">${{ $avgCost ? number_format($avgCost, 2) : '—' }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">Prices are managed per variant below.</p>
                    @else
                        {{-- Manual: editable fields --}}
                        <div class="space-y-4">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="price" id="price" step="0.01" min="0" required
                                        value="{{ old('price', $product->price) }}"
                                        class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                </div>
                                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="sale_price" id="sale_price" step="0.01" min="0"
                                        value="{{ old('sale_price', $product->sale_price) }}"
                                        class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                </div>
                                @error('sale_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-400">Must be less than regular price</p>
                            </div>
                            <div>
                                <label for="cost" class="block text-sm font-medium text-gray-700 mb-1">Cost</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                                    <input type="number" name="cost" id="cost" step="0.01" min="0"
                                        value="{{ old('cost', $product->cost) }}"
                                        class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                </div>
                                @error('cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-400">Your cost (not shown to customers)</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Inventory --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Inventory</h3>

                    @if($product->isPrintful)
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-infinity mr-2 text-admin-teal"></i>
                            Print-on-demand — always in stock
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Inventory is managed by Printful per variant.</p>
                    @else
                        <div class="space-y-4">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
                                <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                                    value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                @error('stock_quantity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="low_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                                <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0"
                                    value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                @error('low_stock_threshold') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-400">Alert when stock reaches this level</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- SEO --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">SEO</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" maxlength="60"
                                value="{{ old('meta_title', $product->meta_title) }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                            @error('meta_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">Recommended: 50-60 characters. <span id="meta-title-counter">0/60</span></p>
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400">Recommended: 150-160 characters. <span id="meta-desc-counter">0/160</span></p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <button type="submit" class="btn-admin-primary w-full">
                        <i class="fas fa-save mr-2"></i>Update Product
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">
                        <a href="{{ route('admin.products.index') }}" class="text-admin-teal hover:underline">Back to Products</a>
                    </p>
                </div>
            </div>
        </div>
    </form>

    {{-- ===================== PRINTFUL SECTIONS (outside main form) ===================== --}}
    @if($product->isPrintful)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 space-y-6">

                {{-- Card 3: Variants --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6" x-data="variantManager()" x-cloak>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">3</span>
                            Variants
                            <span class="text-sm font-normal text-gray-500">(<span x-text="variants.length"></span> total)</span>
                        </h2>
                        <div>
                            <span x-show="saving" class="text-sm text-gray-500"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                            <span x-show="saved" x-transition class="text-sm text-green-600"><i class="fas fa-check mr-1"></i>Saved</span>
                        </div>
                    </div>

                    {{-- Bulk Actions Toolbar --}}
                    <div x-show="selectedIds.length > 0" x-transition class="bg-admin-teal/5 border border-admin-teal/20 rounded-lg p-3 mb-4 flex flex-wrap items-center gap-3">
                        <span class="text-sm font-medium text-gray-700" x-text="selectedIds.length + ' selected'"></span>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="markupPercent" min="0" step="5" placeholder="% markup"
                                class="w-24 text-sm border border-gray-300 rounded px-2 py-1">
                            <button @click="bulkAction('markup_percent', markupPercent)" class="btn-admin-primary btn-admin-sm text-xs">Apply Markup</button>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="flatPrice" min="0" step="0.01" placeholder="$ price"
                                class="w-24 text-sm border border-gray-300 rounded px-2 py-1">
                            <button @click="bulkAction('flat_price', flatPrice)" class="btn-admin-primary btn-admin-sm text-xs">Set Price</button>
                        </div>
                        <button @click="bulkAction('activate')" class="btn-admin-secondary btn-admin-sm text-xs">
                            <i class="fas fa-check mr-1"></i>Activate
                        </button>
                        <button @click="bulkAction('deactivate')" class="btn-admin-secondary btn-admin-sm text-xs">
                            <i class="fas fa-times mr-1"></i>Deactivate
                        </button>
                    </div>

                    @if($product->variants->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b text-left text-gray-600">
                                        <th class="pb-2 w-8">
                                            <input type="checkbox" @change="toggleAll($event.target.checked)"
                                                :checked="selectedIds.length === variants.length && variants.length > 0"
                                                class="rounded border-gray-300">
                                        </th>
                                        <th class="pb-2">Color</th>
                                        <th class="pb-2">Size</th>
                                        <th class="pb-2 text-right">Printful Cost</th>
                                        <th class="pb-2 text-right">Retail Price</th>
                                        <th class="pb-2 text-right">Profit</th>
                                        <th class="pb-2 text-right">Margin</th>
                                        <th class="pb-2 text-center">Active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="variant in variants" :key="variant.id">
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-2">
                                                <input type="checkbox" :value="variant.id"
                                                    :checked="selectedIds.includes(variant.id)"
                                                    @change="toggleSelect(variant.id)"
                                                    class="rounded border-gray-300">
                                            </td>
                                            <td class="py-2">
                                                <div class="flex items-center gap-2">
                                                    <span x-show="variant.color_hex" class="w-4 h-4 rounded-full border border-gray-200"
                                                        :style="'background-color:' + variant.color_hex"></span>
                                                    <span x-text="variant.color_name || '—'"></span>
                                                </div>
                                            </td>
                                            <td class="py-2" x-text="variant.size || '—'"></td>
                                            <td class="py-2 text-right font-mono" x-text="'$' + parseFloat(variant.printful_cost).toFixed(2)"></td>
                                            <td class="py-2 text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <span class="text-gray-400">$</span>
                                                    <input type="number" step="0.01" min="0"
                                                        :value="parseFloat(variant.retail_price).toFixed(2)"
                                                        @change="updateVariant(variant.id, { retail_price: $event.target.value, is_active: variant.is_active })"
                                                        class="w-20 text-right font-mono text-sm border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-admin-teal focus:border-admin-teal">
                                                </div>
                                            </td>
                                            <td class="py-2 text-right font-mono"
                                                :class="parseFloat(variant.profit) >= 0 ? 'text-green-600' : 'text-red-600'"
                                                x-text="'$' + parseFloat(variant.profit).toFixed(2)"></td>
                                            <td class="py-2 text-right font-mono text-xs"
                                                :class="parseFloat(variant.profit_margin) >= 20 ? 'text-green-600' : parseFloat(variant.profit_margin) >= 0 ? 'text-yellow-600' : 'text-red-600'"
                                                x-text="variant.profit_margin + '%'"></td>
                                            <td class="py-2 text-center">
                                                <button @click="updateVariant(variant.id, { retail_price: variant.retail_price, is_active: !variant.is_active })"
                                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                                                    :class="variant.is_active ? 'bg-green-500' : 'bg-gray-300'">
                                                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"
                                                        :class="variant.is_active ? 'translate-x-4' : 'translate-x-0.5'"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No variants configured.</p>
                    @endif
                </div>

                {{-- Card 4: Design Files --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">4</span>
                        Design Files
                    </h2>

                    @if($product->designs->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($product->designs as $design)
                                <div class="border border-gray-200 rounded-lg p-3 text-center">
                                    <img src="{{ asset('storage/' . $design->file_url) }}" alt="{{ $design->placement }}"
                                        class="w-full h-24 object-contain mb-2">
                                    <p class="text-xs font-medium text-gray-700">{{ ucfirst($design->placement) }}</p>
                                    @if($design->printful_file_id)
                                        <span class="text-xs text-green-600"><i class="fas fa-check mr-1"></i>Synced</span>
                                    @else
                                        <span class="text-xs text-yellow-600"><i class="fas fa-clock mr-1"></i>Local only</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.printful.upload-design', $product) }}" method="POST" enctype="multipart/form-data"
                        class="bg-gray-50 rounded-lg p-4">
                        @csrf
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex-1">
                                <input type="file" name="design_file" accept=".png,.jpg,.jpeg,.svg,.pdf" required
                                    class="w-full text-sm border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div class="w-full md:w-40">
                                <select name="placement" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-admin-teal focus:border-admin-teal">
                                    <option value="front">Front</option>
                                    <option value="back">Back</option>
                                    <option value="sleeve_left">Left Sleeve</option>
                                    <option value="sleeve_right">Right Sleeve</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-admin-primary btn-admin-sm">
                                <i class="fas fa-upload mr-1"></i>Upload
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">PNG, JPG, SVG, or PDF. Max 20MB.</p>
                    </form>
                </div>

                {{-- Card 5: Mockups --}}
                <div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-admin-teal text-white text-sm mr-2">5</span>
                            Mockups
                        </h2>
                        @if($product->designs->isNotEmpty())
                            <form action="{{ route('admin.printful.generate-mockups', $product) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn-admin-primary btn-admin-sm"
                                    onclick="return confirm('Generate mockups via Printful? This may take 30-60 seconds.')">
                                    <i class="fas fa-magic mr-1"></i>Generate Mockups
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($product->mockups->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($product->mockups()->ordered()->get() as $mockup)
                                <div class="relative group">
                                    <img src="{{ $mockup->mockup_url }}" alt="Mockup"
                                        class="w-full rounded-lg shadow-sm">
                                    @if($mockup->is_primary)
                                        <span class="absolute top-2 left-2 bg-admin-teal text-white text-xs px-2 py-0.5 rounded">Primary</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No mockups generated yet. Upload a design first, then click "Generate Mockups".</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ===================== DANGER ZONE ===================== --}}
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-red-200 p-4 md:p-6">
            <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
            <p class="text-sm text-gray-700 mb-3">
                Deleting this product will remove it from your catalog. This action cannot be undone.
                @if($product->orderItems()->count() > 0)
                    <strong class="text-red-600">Note: This product has {{ $product->orderItems()->count() }} order(s) associated with it.</strong>
                @endif
            </p>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                onsubmit="return confirm('Are you absolutely sure you want to delete this product? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-admin-danger">
                    <i class="fas fa-trash mr-2"></i>Delete Product
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from product name (only if slug is empty)
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const originalSlug = slugInput.value;

    nameInput.addEventListener('input', function(e) {
        // Only auto-generate if slug hasn't been manually changed
        if (!slugInput.dataset.manuallyChanged) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugInput.value = slug;
        }
    });

    slugInput.addEventListener('input', function() {
        // Mark as manually changed if user modifies the slug
        if (this.value !== originalSlug) {
            this.dataset.manuallyChanged = 'true';
        }
    });

    // Image preview for new uploads
    document.getElementById('images').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        const files = Array.from(e.target.files).slice(0, 5);

        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="New image ${index + 1}" class="w-full h-32 object-cover rounded">
                    <div class="absolute top-1 left-1 bg-blue-600 bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        New Image ${index + 1}
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Character counters for SEO fields
    function updateCounter(inputId, counterId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.getElementById(counterId);

        input.addEventListener('input', () => {
            const length = input.value.length;
            counter.textContent = `${length}/${maxLength}`;
            counter.classList.toggle('text-red-600', length > maxLength);
            counter.classList.toggle('text-gray-500', length <= maxLength);
        });

        // Initial count
        const length = input.value.length;
        counter.textContent = `${length}/${maxLength}`;
        counter.classList.toggle('text-red-600', length > maxLength);
        counter.classList.toggle('text-gray-500', length <= maxLength);
    }

    updateCounter('meta_title', 'meta-title-counter', 60);
    updateCounter('meta_description', 'meta-desc-counter', 160);
</script>

@if($product->isPrintful)
@php
    $variantJson = $product->variants()->ordered()->get()->map(fn ($v) => [
        'id' => $v->id,
        'color_name' => $v->color_name,
        'color_hex' => $v->color_hex,
        'size' => $v->size,
        'printful_cost' => number_format($v->printful_cost, 2, '.', ''),
        'retail_price' => number_format($v->retail_price, 2, '.', ''),
        'profit' => number_format($v->profit, 2, '.', ''),
        'profit_margin' => $v->profit_margin,
        'is_active' => $v->is_active,
    ]);
@endphp
<script>
function variantManager() {
    return {
        variants: @json($variantJson),
        selectedIds: [],
        markupPercent: 50,
        flatPrice: '',
        saving: false,
        saved: false,

        toggleAll(checked) {
            this.selectedIds = checked ? this.variants.map(v => v.id) : [];
        },

        toggleSelect(id) {
            const idx = this.selectedIds.indexOf(id);
            idx === -1 ? this.selectedIds.push(id) : this.selectedIds.splice(idx, 1);
        },

        async updateVariant(id, data) {
            this.saving = true;
            this.saved = false;
            try {
                const res = await fetch(`/admin/products/{{ $product->id }}/variants/${id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const json = await res.json();
                if (json.success) {
                    const v = this.variants.find(v => v.id === id);
                    if (v) {
                        v.retail_price = json.variant.retail_price;
                        v.is_active = json.variant.is_active;
                        v.profit = json.variant.profit;
                        v.profit_margin = json.variant.profit_margin;
                    }
                    this.saved = true;
                    setTimeout(() => this.saved = false, 2000);
                }
            } catch (e) { console.error(e); }
            this.saving = false;
        },

        async bulkAction(action, value = null) {
            if (this.selectedIds.length === 0) return;
            this.saving = true;
            this.saved = false;
            try {
                const res = await fetch(`/admin/products/{{ $product->id }}/variants/bulk-update`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ variant_ids: this.selectedIds, action, value }),
                });
                const json = await res.json();
                if (json.success) {
                    this.variants = json.variants;
                    this.selectedIds = [];
                    this.saved = true;
                    setTimeout(() => this.saved = false, 2000);
                }
            } catch (e) { console.error(e); }
            this.saving = false;
        }
    };
}

function number_format_js(val) {
    return parseFloat(val).toFixed(2);
}
</script>
@endif

<x-tinymce-init />
@endpush
@endsection
