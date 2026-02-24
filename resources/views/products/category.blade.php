<x-app-layout>
    @section('title', $categoryModel->name . ' Products')
    @section('meta_description', $categoryModel->description ?: 'Browse our ' . strtolower($categoryModel->name) . ' products')

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 text-sm" data-animate="fade-in">
                <a href="{{ route('home') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Home</a>
                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <a href="{{ route('products.index') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Products</a>

                @foreach($categoryModel->ancestors() as $ancestor)
                    <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                    <a href="{{ route('products.category', $ancestor->slug) }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">
                        {{ $ancestor->name }}
                    </a>
                @endforeach

                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <span style="color: var(--on-surface);" class="font-medium">{{ $categoryModel->name }}</span>
            </nav>

            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">{{ $categoryModel->name }}</h1>
                <p class="text-lg max-w-2xl mx-auto" style="color: var(--on-surface-muted);">{{ $categoryModel->description ?: 'Browse products in this category' }}</p>
            </div>

            {{-- Category Filter Pills --}}
            <div class="mb-6 overflow-x-auto scrollbar-hide" data-animate="fade-up">
                <div class="flex gap-2 pb-2 min-w-max justify-center">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap hover:bg-earth-primary/10" style="background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);">
                        All Products
                    </a>
                    @php
                        $ancestors = $categoryModel->ancestors();
                        $topLevelCategory = $ancestors->isNotEmpty() ? $ancestors->first() : $categoryModel;
                    @endphp
                    @foreach($categories as $cat)
                        <a href="{{ route('products.category', $cat->slug) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 whitespace-nowrap {{ $topLevelCategory->slug == $cat->slug ? 'bg-gradient-to-r from-earth-primary to-earth-green text-white shadow-md' : 'hover:bg-earth-primary/10' }}" style="{{ $topLevelCategory->slug == $cat->slug ? '' : 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Subcategories --}}
            @if($categoryModel->children->isNotEmpty())
                <div class="mb-10" data-animate="stagger">
                    <h3 class="font-display font-semibold text-sm uppercase tracking-wider mb-4" style="color: var(--on-surface-muted);">{{ $categoryModel->name }} Subcategories</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($categoryModel->children as $child)
                            <a href="{{ route('products.category', $child->slug) }}"
                               class="card-glass p-4 rounded-xl hover:-translate-y-0.5 transition-all duration-200 group">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-sm" style="color: var(--on-surface);">{{ $child->name }}</span>
                                    @if($child->children->isNotEmpty())
                                        <i class="fas fa-chevron-right text-xs text-earth-primary/50 group-hover:text-earth-primary transition-colors"></i>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Filters Panel --}}
            @if(isset($filterOptions))
            <div class="mb-8" data-animate="fade-up" x-data="{ filtersOpen: {{ request()->hasAny(['colors', 'sizes', 'min_price', 'max_price', 'in_stock']) ? 'true' : 'false' }} }">
                <button @click="filtersOpen = !filtersOpen" class="flex items-center gap-2 mx-auto px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);">
                    <i class="fas fa-sliders-h"></i>
                    <span x-text="filtersOpen ? 'Hide Filters' : 'Show Filters'"></span>
                    @if(request()->hasAny(['colors', 'sizes', 'min_price', 'max_price', 'in_stock']))
                        <span class="bg-earth-primary text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">!</span>
                    @endif
                </button>

                <div x-show="filtersOpen" x-transition class="mt-4 card-glass rounded-2xl p-6">
                    <form method="GET" action="{{ route('products.category', $categoryModel->slug) }}">
                        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            {{-- Colors --}}
                            @if($filterOptions['colors']->isNotEmpty())
                            <div>
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">Colors</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($filterOptions['colors'] as $color)
                                        @php $selected = in_array($color->color_name, request('colors', [])); @endphp
                                        <label class="cursor-pointer" title="{{ $color->color_name }}">
                                            <input type="checkbox" name="colors[]" value="{{ $color->color_name }}" {{ $selected ? 'checked' : '' }} class="sr-only peer">
                                            <span class="block w-8 h-8 rounded-full border-2 transition-all peer-checked:ring-2 peer-checked:ring-earth-primary peer-checked:ring-offset-2 {{ $selected ? 'border-earth-primary' : 'border-gray-300/50' }}"
                                                style="background-color: {{ $color->color_hex ?? '#ccc' }};"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Sizes --}}
                            @if($filterOptions['sizes']->isNotEmpty())
                            <div>
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">Sizes</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($filterOptions['sizes'] as $size)
                                        @php $selected = in_array($size, request('sizes', [])); @endphp
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="sizes[]" value="{{ $size }}" {{ $selected ? 'checked' : '' }} class="sr-only peer">
                                            <span class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all peer-checked:bg-earth-primary peer-checked:text-white {{ $selected ? 'bg-earth-primary text-white' : '' }}"
                                                style="{{ !$selected ? 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' : '' }}">{{ $size }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Price Range --}}
                            <div>
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">Price Range</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-sm" style="color: var(--on-surface-muted);">$</span>
                                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ floor($filterOptions['price_range']['min']) }}" min="0" step="1"
                                            class="glass-input w-full pl-6 pr-2 py-2 rounded-lg text-sm" style="color: var(--on-surface);">
                                    </div>
                                    <span style="color: var(--on-surface-muted);">—</span>
                                    <div class="relative flex-1">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-sm" style="color: var(--on-surface-muted);">$</span>
                                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ ceil($filterOptions['price_range']['max']) }}" min="0" step="1"
                                            class="glass-input w-full pl-6 pr-2 py-2 rounded-lg text-sm" style="color: var(--on-surface);">
                                    </div>
                                </div>
                            </div>

                            {{-- In Stock + Actions --}}
                            <div>
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">Availability</label>
                                <label class="flex items-center gap-2 mb-4 cursor-pointer">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-earth-primary focus:ring-earth-primary">
                                    <span class="text-sm" style="color: var(--on-surface);">In Stock Only</span>
                                </label>
                                <div class="flex gap-2">
                                    <button type="submit" class="btn-gradient text-sm px-4 py-2 rounded-xl">Apply Filters</button>
                                    <a href="{{ route('products.category', $categoryModel->slug) }}" class="btn-glass text-sm px-4 py-2 rounded-xl" style="color: var(--on-surface);">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Sort + Results Count --}}
            <div class="flex items-center justify-between mb-6" data-animate="fade-up">
                <p class="text-sm" style="color: var(--on-surface-muted);">{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</p>
                <form method="GET" action="{{ route('products.category', $categoryModel->slug) }}" class="flex items-center gap-2">
                    @if(request('on_sale'))<input type="hidden" name="on_sale" value="{{ request('on_sale') }}">@endif
                    @foreach(request('colors', []) as $c)<input type="hidden" name="colors[]" value="{{ $c }}">@endforeach
                    @foreach(request('sizes', []) as $s)<input type="hidden" name="sizes[]" value="{{ $s }}">@endforeach
                    @if(request('min_price'))<input type="hidden" name="min_price" value="{{ request('min_price') }}">@endif
                    @if(request('max_price'))<input type="hidden" name="max_price" value="{{ request('max_price') }}">@endif
                    @if(request('in_stock'))<input type="hidden" name="in_stock" value="{{ request('in_stock') }}">@endif
                    <label for="sort" class="text-sm font-medium" style="color: var(--on-surface-muted);">Sort by</label>
                    <select name="sort" id="sort" onchange="this.form.submit()"
                            class="input-glass rounded-lg text-sm py-1.5 pl-3 pr-8 w-auto">
                        <option value="">Name</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    </select>
                </form>
            </div>

            {{-- Products Grid --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" data-animate="stagger">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-16" data-animate="fade-up">
                    <div class="w-20 h-20 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-box-open text-3xl text-earth-primary/50"></i>
                    </div>
                    <p class="text-lg font-display font-semibold mb-2" style="color: var(--on-surface);">No products in this category</p>
                    <p class="mb-6" style="color: var(--on-surface-muted);">Check out our other categories or browse all products.</p>
                    <a href="{{ route('products.index') }}" class="btn-gradient">
                        Browse All Products
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
