@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Products</h1>
                <p class="text-gray-600 mt-1">Manage your product catalog and inventory</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.products.categories.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-folder mr-2"></i>Manage Product Categories
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>New Product
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards: Mobile-optimized (2 cols mobile, 3 cols tablet, 5 cols desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
                <a href="{{ route('admin.products.index') }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-box text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index', ['status' => 'active']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Active</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['active']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index', ['status' => 'inactive']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Inactive</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-600 mt-2">{{ number_format($stats['inactive']) }}</p>
                        </div>
                        <div class="bg-gray-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-times-circle text-gray-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index', ['stock_status' => 'low_stock']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Low Stock</p>
                            <p class="text-2xl md:text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['low_stock']) }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.products.index', ['stock_status' => 'out_of_stock']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Out of Stock</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['out_of_stock']) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-ban text-red-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Filters Section - Desktop Only -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Name, SKU, or Barcode"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="category"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Categories</option>
                                @foreach($categories as $slug => $name)
                                    <option value="{{ $slug }}" {{ request('category') == $slug ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Stock Status Filter -->
                        <div>
                            <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                            <select name="stock_status" id="stock_status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Stock Levels</option>
                                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>

                        <!-- Featured Filter -->
                        <div>
                            <label for="featured" class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                            <select name="featured" id="featured"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Products</option>
                                <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                                <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Not Featured</option>
                            </select>
                        </div>

                        <!-- Filter Buttons (Full Width on Next Row) -->
                        <div class="md:col-span-5 flex gap-2">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn-admin-secondary">
                                Clear
                            </a>
                            <a href="{{ route('admin.products.export', request()->query()) }}" class="btn-admin-success">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.products.index') }}">
                <!-- Search -->
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Name, SKU, or Barcode"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="mobile-category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" id="mobile-category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Categories</option>
                        @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" {{ request('category') == $slug ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="mobile-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="mobile-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Stock Status Filter -->
                <div>
                    <label for="mobile-stock-status" class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                    <select name="stock_status" id="mobile-stock-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Stock Levels</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <!-- Featured Filter -->
                <div>
                    <label for="mobile-featured" class="block text-sm font-medium text-gray-700 mb-2">Featured</label>
                    <select name="featured" id="mobile-featured"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Products</option>
                        <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                        <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Not Featured</option>
                    </select>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Bulk Actions Bar (Hidden by default, shown when items selected) -->
            <div id="bulk-actions-bar" class="hidden bg-abs-primary text-white p-4 rounded-lg shadow-lg mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="font-semibold"><span id="selected-count">0</span> item(s) selected</span>
                        <form id="bulk-actions-form" action="{{ route('admin.products.bulk') }}" method="POST" class="flex items-center space-x-3">
                            @csrf
                            <select id="bulk-action" class="px-4 py-2 rounded border border-gray-300 text-gray-900 focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">Choose Action</option>
                                <option value="publish">Publish Selected</option>
                                <option value="unpublish">Unpublish Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" onclick="applyBulkAction(event)" class="btn-admin-secondary">
                                Apply
                            </button>
                            <input type="hidden" id="bulk-action-input" name="action" value="">
                            <div id="bulk-ids-container"></div>
                        </form>
                    </div>
                    <button onclick="document.querySelectorAll('.select-item').forEach(cb => cb.checked = false); document.getElementById('select-all').checked = false; this.closest('#bulk-actions-bar').classList.add('hidden');" class="link-admin text-white hover:text-gray-200">
                        <i class="fas fa-times"></i> Clear Selection
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($products->count() > 0)
                        <!-- Mobile Cards View - Visible only on mobile -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($products as $product)
                                <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow duration-200 relative">

                                    <!-- Checkbox in Top-Right Corner -->
                                    <div class="absolute top-4 right-4">
                                        <input type="checkbox" class="select-item rounded border-gray-300 text-admin-teal focus:ring-admin-teal w-5 h-5"
                                               value="{{ $product->id }}" aria-label="Select product {{ $product->name }}">
                                    </div>

                                    <div class="space-y-3 pr-8">
                                        <!-- Product Image & Name -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Product</p>
                                            <div class="mt-1 flex items-center">
                                                @if($product->mockups && $product->mockups->count() > 0)
                                                    <img src="{{ $product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()->mockup_url }}" alt="{{ $product->name }}"
                                                         class="w-16 h-16 rounded object-cover mr-3">
                                                @elseif($product->images && is_array($product->images) && count($product->images) > 0)
                                                    <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}"
                                                         class="w-16 h-16 rounded object-cover mr-3">
                                                @else
                                                    <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                        <i class="fas fa-image text-gray-400"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                                    <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Price</p>
                                            <div class="mt-1">
                                                @if($product->sale_price)
                                                    <div class="text-lg font-semibold text-green-600">${{ number_format($product->sale_price, 2) }}</div>
                                                    <div class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</div>
                                                @else
                                                    <div class="text-lg font-semibold text-gray-900">${{ number_format($product->price, 2) }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Stock -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Stock</p>
                                            <div class="mt-1">
                                                <div class="text-sm text-gray-900 mb-1">{{ $product->stock_quantity }} units</div>
                                                @php
                                                    $stockColors = 'bg-gray-100 text-gray-800';
                                                    if ($product->stock_quantity == 0) {
                                                        $stockColors = 'bg-red-100 text-red-800';
                                                        $stockLabel = 'Out of Stock';
                                                    } elseif ($product->stock_quantity <= $product->low_stock_threshold) {
                                                        $stockColors = 'bg-yellow-100 text-yellow-800';
                                                        $stockLabel = 'Low Stock';
                                                    } else {
                                                        $stockColors = 'bg-green-100 text-green-800';
                                                        $stockLabel = 'In Stock';
                                                    }
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockColors }}">
                                                    {{ $stockLabel }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Status</p>
                                            <div class="mt-1">
                                                @php
                                                    $statusColor = $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                                @if($product->featured)
                                                    <i class="fas fa-star text-yellow-500 ml-2"></i>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table - Hidden on mobile -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary" aria-label="Select all products">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($products as $product)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <input type="checkbox" class="select-item rounded border-gray-300 text-abs-primary focus:ring-abs-primary" value="{{ $product->id }}" aria-label="Select product {{ $product->name }}">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if($product->mockups && $product->mockups->count() > 0)
                                                        <img src="{{ $product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()->mockup_url }}" alt="{{ $product->name }}"
                                                             class="w-12 h-12 rounded object-cover mr-3">
                                                    @elseif($product->images && is_array($product->images) && count($product->images) > 0)
                                                        <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}"
                                                             class="w-12 h-12 rounded object-cover mr-3">
                                                    @else
                                                        <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                            <i class="fas fa-image text-gray-400"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('admin.products.edit', $product) }}" class="link-admin-primary text-sm font-semibold text-gray-900">{{ $product->name }}</a>
                                                        <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden lg:table-cell px-6 py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @if($product->categories->isNotEmpty())
                                                        @foreach($product->categories as $cat)
                                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs
                                                                {{ $cat->pivot->is_primary ? 'bg-admin-teal text-white' : 'bg-gray-200 text-gray-700' }}">
                                                                {{ $cat->name }}
                                                                @if($cat->pivot->is_primary)
                                                                    <i class="fas fa-star ml-1 text-xs"></i>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-sm text-gray-400">No categories</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($product->sale_price)
                                                    <div class="text-sm font-semibold text-green-600">${{ number_format($product->sale_price, 2) }}</div>
                                                    <div class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</div>
                                                @else
                                                    <div class="text-sm font-semibold text-gray-900">${{ number_format($product->price, 2) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $product->stock_quantity }} units</div>
                                                @php
                                                    $stockColors = 'bg-gray-100 text-gray-800';
                                                    if ($product->stock_quantity == 0) {
                                                        $stockColors = 'bg-red-100 text-red-800';
                                                        $stockLabel = 'Out of Stock';
                                                    } elseif ($product->stock_quantity <= $product->low_stock_threshold) {
                                                        $stockColors = 'bg-yellow-100 text-yellow-800';
                                                        $stockLabel = 'Low Stock';
                                                    } else {
                                                        $stockColors = 'bg-green-100 text-green-800';
                                                        $stockLabel = 'In Stock';
                                                    }
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stockColors }}">
                                                    {{ $stockLabel }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColor = $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                    {{ ucfirst($product->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($product->featured)
                                                    <i class="fas fa-star text-yellow-500"></i>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.products.edit', $product) }}"
                                                   aria-label="Edit product"
                                                   class="link-admin-info mr-3">
                                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                                </a>
                                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            aria-label="Delete product"
                                                            class="link-admin-danger">
                                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <i class="fas fa-box text-gray-400 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                            <p class="text-gray-500 mb-4">
                                @if(request()->hasAny(['search', 'category', 'status', 'stock_status', 'featured']))
                                    No products match your current filters. Try adjusting your search criteria.
                                @else
                                    Create your first product to get started with your catalog.
                                @endif
                            </p>
                            <a href="{{ route('admin.products.create') }}" class="btn-admin-primary">
                                <i class="fas fa-plus mr-2"></i>Create Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
