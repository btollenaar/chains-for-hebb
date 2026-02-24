<x-app-layout>
    @section('title', $product->name)
    @section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description), 155))
    @section('og_type', 'product')
    @section('og_title', $product->meta_title ?? $product->name)
    @section('og_description', $product->meta_description ?? Str::limit(strip_tags($product->description), 155))
    @if($product->mockups->isNotEmpty())
        @section('og_image', $product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()->mockup_url)
    @elseif($product->first_image_url)
        @section('og_image', $product->first_image_url)
    @endif
    @section('canonical_url', route('products.show', $product->slug))

    @push('head')
    <script type="application/ld+json">
    @php
        $activeVariants = $product->activeVariants;
        $minPrice = $activeVariants->min('retail_price') ?? $product->price;
        $maxPrice = $activeVariants->max('retail_price') ?? $product->price;
        $reviewCount = $product->reviews->where('status', 'approved')->count();
        $avgRating = $reviewCount > 0 ? round($product->reviews->where('status', 'approved')->avg('rating'), 1) : null;

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => Str::limit(strip_tags($product->description), 500),
            'url' => route('products.show', $product->slug),
            'sku' => $product->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => config('app.name'),
            ],
            'offers' => [
                '@type' => $minPrice === $maxPrice ? 'Offer' : 'AggregateOffer',
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
            ],
        ];

        if ($minPrice === $maxPrice) {
            $jsonLd['offers']['price'] = number_format($minPrice, 2, '.', '');
        } else {
            $jsonLd['offers']['lowPrice'] = number_format($minPrice, 2, '.', '');
            $jsonLd['offers']['highPrice'] = number_format($maxPrice, 2, '.', '');
        }

        if ($product->mockups->isNotEmpty()) {
            $jsonLd['image'] = $product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()->mockup_url;
        } elseif ($product->first_image_url) {
            $jsonLd['image'] = $product->first_image_url;
        }

        if ($avgRating && $reviewCount > 0) {
            $jsonLd['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $avgRating,
                'reviewCount' => $reviewCount,
            ];
        }
    @endphp
    {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endpush

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="mb-8 flex items-center gap-2 text-sm" data-animate="fade-in">
                <a href="{{ route('home') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Home</a>
                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <a href="{{ route('products.index') }}" class="hover:text-earth-primary transition-colors" style="color: var(--on-surface-muted);">Products</a>
                <i class="fas fa-chevron-right text-[10px]" style="color: var(--on-surface-muted); opacity: 0.5;"></i>
                <span style="color: var(--on-surface);" class="font-medium">{{ $product->name }}</span>
            </nav>

            {{-- Product Detail Grid --}}
            @if($product->isPrintful && $product->activeVariants->count() > 0)
                {{-- Printful POD Product with Variants --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16" data-animate="fade-up"
                     x-data="variantSelector()">

                    {{-- Product Images / Mockups --}}
                    <div>
                        @if($product->mockups->count() > 0)
                            <div class="card-glass rounded-2xl overflow-hidden mb-4">
                                <img :src="activeImage" alt="{{ $product->name }}" class="w-full aspect-square object-cover transition-all duration-300">
                            </div>

                            @if($product->mockups->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->mockups()->ordered()->get() as $mockup)
                                        <button @click="activeImage = '{{ $mockup->mockup_url }}'"
                                                class="rounded-xl overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                                                :class="activeImage === '{{ $mockup->mockup_url }}' ? 'border-earth-primary shadow-md' : 'border-transparent opacity-70 hover:opacity-100'"
                                                style="background: var(--glass-bg);">
                                            <img src="{{ $mockup->mockup_url }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($product->images && count($product->images) > 0)
                            <div class="card-glass rounded-2xl overflow-hidden mb-4">
                                <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover">
                            </div>
                        @else
                            <div class="card-glass rounded-2xl overflow-hidden">
                                <div class="w-full aspect-square flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                                    <i class="fas fa-tshirt text-6xl" style="color: var(--on-surface-muted); opacity: 0.3;"></i>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info with Variant Selector --}}
                    <div>
                        <h1 class="text-fluid-2xl font-display font-bold mb-4" style="color: var(--on-surface);">{{ $product->name }}</h1>

                        {{-- Dynamic Price --}}
                        <div class="mb-6">
                            <template x-if="selectedVariant">
                                <span class="text-3xl font-bold" style="color: var(--on-surface);" x-text="'$' + parseFloat(selectedVariant.retail_price).toFixed(2)"></span>
                            </template>
                            <template x-if="!selectedVariant">
                                <span class="text-3xl font-bold" style="color: var(--on-surface);">{{ $product->price_range ?? '$' . number_format($product->price, 2) }}</span>
                            </template>
                        </div>

                        {{-- Color Selector --}}
                        @php
                            $colors = $product->activeVariants->whereNotNull('color_name')->unique('color_name');
                        @endphp
                        @if($colors->count() > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">
                                    Color: <span x-text="selectedColor" class="font-normal" style="color: var(--on-surface-muted);"></span>
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($colors as $variant)
                                        <button type="button"
                                                @click="selectColor('{{ $variant->color_name }}')"
                                                class="w-10 h-10 rounded-full border-2 transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-earth-primary focus:ring-offset-2"
                                                :class="selectedColor === '{{ $variant->color_name }}' ? 'border-earth-primary shadow-lg scale-110' : 'border-gray-300/50'"
                                                style="background-color: {{ $variant->color_hex ?? '#ccc' }};"
                                                title="{{ $variant->color_name }}">
                                            <span x-show="selectedColor === '{{ $variant->color_name }}'" class="flex items-center justify-center">
                                                @php
                                                    $hex = ltrim($variant->color_hex ?? '#ccc', '#');
                                                    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                                                    $r = hexdec(substr($hex, 0, 2)); $g = hexdec(substr($hex, 2, 2)); $b = hexdec(substr($hex, 4, 2));
                                                    $isLight = (($r * 299 + $g * 587 + $b * 114) / 1000) > 128;
                                                @endphp
                                                <i class="fas fa-check text-xs" style="color: {{ $isLight ? '#000' : '#fff' }};"></i>
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Size Selector --}}
                        @php
                            $sizes = $product->activeVariants->whereNotNull('size')->unique('size')->sortBy('sort_order');
                        @endphp
                        @if($sizes->count() > 0)
                            <div class="mb-6">
                                <label class="block text-sm font-semibold mb-3" style="color: var(--on-surface);">
                                    Size: <span x-text="selectedSize" class="font-normal" style="color: var(--on-surface-muted);"></span>
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="size in availableSizes" :key="size">
                                        <button type="button"
                                                @click="selectSize(size)"
                                                class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-earth-primary"
                                                :class="selectedSize === size
                                                    ? 'bg-earth-primary text-white shadow-lg'
                                                    : 'hover:bg-earth-primary/10'"
                                                :style="selectedSize !== size ? 'background: var(--glass-bg); color: var(--on-surface); border: 1px solid var(--glass-border);' : ''"
                                                x-text="size">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        @endif

                        {{-- Variant Status --}}
                        <div class="mb-6">
                            <template x-if="selectedVariant && selectedVariant.stock_status === 'in_stock'">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-earth-success/10 text-earth-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="text-sm font-semibold">In Stock</span>
                                </div>
                            </template>
                            <template x-if="selectedVariant && selectedVariant.stock_status !== 'in_stock'">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-500">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="text-sm font-semibold">Out of Stock</span>
                                </div>
                            </template>
                            <template x-if="!selectedVariant && (selectedColor || selectedSize)">
                                <p class="text-sm text-earth-amber font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Please select all options
                                </p>
                            </template>
                        </div>

                        {{-- Description --}}
                        <div class="mb-8 prose max-w-none" style="color: var(--on-surface-muted);">
                            {!! $product->description !!}
                        </div>

                        {{-- Add to Cart --}}
                        <form action="{{ route('cart.add') }}" method="POST" class="mb-8"
                              @submit.prevent="addToCart($el)">
                            @csrf
                            <input type="hidden" name="item_type" value="product">
                            <input type="hidden" name="item_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_variant_id" :value="selectedVariant ? selectedVariant.id : ''">

                            <div class="flex items-end gap-4">
                                <div>
                                    <label for="quantity" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" min="1" value="1"
                                           class="glass-input w-24 rounded-xl text-center" style="color: var(--on-surface);">
                                </div>

                                <button type="submit" class="btn-gradient btn-lg"
                                        :disabled="!selectedVariant || selectedVariant.stock_status !== 'in_stock'"
                                        :class="{ 'opacity-50 cursor-not-allowed': !selectedVariant || selectedVariant.stock_status !== 'in_stock' }">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    <span x-show="!cartAdded">Add to Cart</span>
                                    <span x-show="cartAdded" x-cloak><i class="fas fa-check mr-1"></i>Added!</span>
                                </button>
                            </div>
                        </form>

                        {{-- SKU --}}
                        <template x-if="selectedVariant">
                            <p class="text-sm" style="color: var(--on-surface-muted);">SKU: <span x-text="selectedVariant.sku"></span></p>
                        </template>
                    </div>
                </div>
            @else
                {{-- Standard Product (non-variant) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16" data-animate="fade-up">
                    {{-- Product Images --}}
                    <div x-data="{ activeImage: '{{ $product->first_image_url }}', activeIndex: 0 }">
                        @if($product->images && count($product->images) > 0)
                            <a :href="activeImage"
                               data-glightbox="gallery:product"
                               data-title="{{ $product->name }}"
                               class="gallery-image-wrapper block card-glass rounded-2xl overflow-hidden mb-4">
                                <img :src="activeImage" alt="{{ $product->name }}" class="w-full aspect-square object-cover transition-all duration-300">
                                <span class="zoom-icon"><i class="fas fa-search-plus"></i></span>
                            </a>

                            @foreach($product->image_urls as $index => $imageUrl)
                                @if($index > 0)
                                    <a href="{{ $imageUrl }}" data-glightbox="gallery:product" data-title="{{ $product->name }}" class="hidden"></a>
                                @endif
                            @endforeach

                            @if(count($product->images) > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->image_urls as $index => $imageUrl)
                                        <button @click="activeImage = '{{ $imageUrl }}'; activeIndex = {{ $index }}"
                                                class="gallery-image-wrapper rounded-xl overflow-hidden border-2 transition-all duration-200 focus:outline-none"
                                                :class="activeImage === '{{ $imageUrl }}' ? 'border-earth-primary shadow-md' : 'border-transparent opacity-70 hover:opacity-100'"
                                                style="background: var(--glass-bg);">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-20 object-cover">
                                            <span class="zoom-icon"><i class="fas fa-search-plus"></i></span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="card-glass rounded-2xl overflow-hidden">
                                <div class="w-full aspect-square flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                                    <i class="fas fa-box text-6xl" style="color: var(--on-surface-muted); opacity: 0.3;"></i>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Product Info --}}
                    <div>
                        <h1 class="text-fluid-2xl font-display font-bold mb-4" style="color: var(--on-surface);">{{ $product->name }}</h1>

                        {{-- Price --}}
                        <div class="mb-6">
                            @if($product->isOnSale)
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl font-bold text-gradient-accent">${{ number_format($product->currentPrice, 2) }}</span>
                                    <span class="text-xl line-through" style="color: var(--on-surface-muted);">${{ number_format($product->price, 2) }}</span>
                                    <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1 rounded-lg text-sm font-bold">SALE</span>
                                </div>
                            @else
                                <span class="text-3xl font-bold" style="color: var(--on-surface);">${{ number_format($product->currentPrice, 2) }}</span>
                            @endif
                        </div>

                        {{-- Stock Status --}}
                        <div class="mb-6">
                            @if($product->isInStock)
                                @if($product->isLowStock)
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-earth-amber/10 text-earth-amber">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span class="text-sm font-semibold">Low Stock - Only {{ $product->stock_quantity }} left!</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-earth-success/10 text-earth-success">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="text-sm font-semibold">In Stock</span>
                                    </div>
                                @endif
                            @else
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-500">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="text-sm font-semibold">Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        {{-- Description --}}
                        <div class="mb-8 prose max-w-none" style="color: var(--on-surface-muted);">
                            {!! $product->description !!}
                        </div>

                        {{-- Add to Cart --}}
                        @if($product->isInStock)
                            <form action="{{ route('cart.add') }}" method="POST" class="mb-8"
                                  x-data="{ added: false }"
                                  @submit.prevent="added = true; $el.submit(); setTimeout(() => added = false, 2000)">
                                @csrf
                                <input type="hidden" name="item_type" value="product">
                                <input type="hidden" name="item_id" value="{{ $product->id }}">

                                <div class="flex items-end gap-4">
                                    <div>
                                        <label for="quantity" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" min="1" max="{{ $product->stock_quantity }}" value="1"
                                               class="glass-input w-24 rounded-xl text-center" style="color: var(--on-surface);">
                                    </div>

                                    <button type="submit" class="btn-gradient btn-lg">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        <span x-show="!added">Add to Cart</span>
                                        <span x-show="added" x-cloak><i class="fas fa-check mr-1"></i>Added!</span>
                                    </button>
                                </div>
                            </form>
                        @endif

                        {{-- SKU --}}
                        @if($product->sku)
                            <p class="text-sm" style="color: var(--on-surface-muted);">SKU: {{ $product->sku }}</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Long Description --}}
            @if($product->long_description)
                <div class="card-glass rounded-2xl p-8 mb-16" data-animate="fade-up">
                    <h2 class="text-2xl font-display font-bold mb-6" style="color: var(--on-surface);">Product Details</h2>
                    <div class="prose max-w-none" style="color: var(--on-surface-muted);">{!! $product->long_description !!}</div>
                </div>
            @endif

            {{-- Customer Reviews --}}
            <div class="mb-16" data-animate="fade-up">
                <x-reviews-section :reviewable="$product" type="product" />
            </div>

            {{-- Customers Also Bought --}}
            @if(isset($alsoBought) && $alsoBought->count() > 0)
                <div class="mb-16" data-animate="fade-up">
                    <div class="flex items-end justify-between mb-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-2">Frequently Bought Together</p>
                            <h2 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Customers Also Bought</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate="stagger">
                        @foreach($alsoBought as $abProduct)
                            <x-product-card :product="$abProduct" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Similar Products --}}
            @if(isset($similarProducts) && $similarProducts->count() > 0)
                <div class="mb-16" data-animate="fade-up">
                    <div class="flex items-end justify-between mb-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gradient-accent mb-2">Compare Options</p>
                            <h2 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Similar Products</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate="stagger">
                        @foreach($similarProducts as $simProduct)
                            <x-product-card :product="$simProduct" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Related Products --}}
            @if($relatedProducts && $relatedProducts->count() > 0)
                <div data-animate="fade-up">
                    <div class="flex items-end justify-between mb-8">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-2">You may also like</p>
                            <h2 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Related Products</h2>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-animate="stagger">
                        @foreach($relatedProducts as $relatedProduct)
                            <x-product-card :product="$relatedProduct" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        {{-- GA4: view_item --}}
        @if(config('services.google.analytics_id'))
        gtag('event', 'view_item', {
            currency: 'USD',
            value: {{ $product->currentPrice }},
            items: [{
                item_id: '{{ $product->sku ?? $product->id }}',
                item_name: @json($product->name),
                price: {{ $product->currentPrice }},
                item_category: @json($product->primaryCategory?->name ?? ''),
            }]
        });
        @endif

        {{-- Meta Pixel: ViewContent --}}
        @if(config('services.meta.pixel_id'))
        fbq('track', 'ViewContent', {
            content_ids: ['{{ $product->sku ?? $product->id }}'],
            content_type: 'product',
            content_name: @json($product->name),
            value: {{ $product->currentPrice }},
            currency: 'USD'
        });
        @endif

        @if($product->isPrintful && $product->activeVariants->count() > 0)
        function variantSelector() {
            const variants = @json($product->activeVariants->values());
            const primaryMockup = @json($product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()?->mockup_url ?? $product->first_image_url);

            return {
                variants: variants,
                selectedColor: '',
                selectedSize: '',
                selectedVariant: null,
                activeImage: primaryMockup,
                cartAdded: false,

                get availableSizes() {
                    if (!this.selectedColor) {
                        return [...new Set(this.variants.filter(v => v.size).map(v => v.size))];
                    }
                    return [...new Set(
                        this.variants
                            .filter(v => v.color_name === this.selectedColor && v.size)
                            .map(v => v.size)
                    )];
                },

                selectColor(color) {
                    this.selectedColor = color;
                    this.updateSelectedVariant();
                },

                selectSize(size) {
                    this.selectedSize = size;
                    this.updateSelectedVariant();
                },

                updateSelectedVariant() {
                    this.selectedVariant = this.variants.find(v =>
                        (!this.selectedColor || v.color_name === this.selectedColor) &&
                        (!this.selectedSize || v.size === this.selectedSize)
                    ) || null;
                },

                addToCart(form) {
                    if (!this.selectedVariant || this.selectedVariant.stock_status !== 'in_stock') return;
                    this.cartAdded = true;
                    window.addToCartAjax(form);
                    setTimeout(() => this.cartAdded = false, 2000);
                }
            };
        }
        @endif
    </script>
    @endpush
</x-app-layout>
