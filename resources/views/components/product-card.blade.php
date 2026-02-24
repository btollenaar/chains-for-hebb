@props(['product'])

<div class="card-glass group overflow-hidden rounded-2xl transition-all duration-300 hover:-translate-y-1 hover:shadow-glass-lg"
     x-data="{ added: false }">
    {{-- Product Image --}}
    <div class="relative overflow-hidden">
        <a href="{{ route('products.show', $product->slug) }}">
            @if($product->isPrintful && $product->mockups && $product->mockups->count() > 0)
                <img src="{{ $product->mockups->where('is_primary', true)->first()?->mockup_url ?? $product->mockups->first()->mockup_url }}"
                     alt="{{ $product->name }}" class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            @elseif($product->images && count($product->images) > 0)
                <img src="{{ $product->first_image_url }}" alt="{{ $product->name }}" class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            @else
                <div class="w-full h-52 flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                    <i class="fas fa-box text-4xl" style="color: var(--on-surface-muted);"></i>
                </div>
            @endif
        </a>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if($product->featured)
                <span class="badge-glass text-xs font-bold uppercase tracking-wider px-2.5 py-1">
                    <i class="fas fa-star text-earth-amber mr-1"></i>Featured
                </span>
            @endif
        </div>

        @if($product->isOnSale)
            <span class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-pink-500 text-white px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider shadow-lg">
                Sale
            </span>
        @endif

        {{-- Wishlist Heart --}}
        @auth
            <button
                x-data="{ wishlisted: {{ \App\Models\Wishlist::isWishlisted(\App\Models\Product::class, $product->id, auth()->id()) ? 'true' : 'false' }} }"
                @click.prevent="
                    fetch('{{ route('wishlist.toggle') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ item_type: 'product', item_id: {{ $product->id }} })
                    }).then(r => r.json()).then(d => { wishlisted = d.added; window.notify(d.message, 'success'); })
                "
                class="absolute {{ $product->isOnSale ? 'top-12' : 'top-3' }} right-3 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm shadow-md transition-all hover:scale-110 z-10"
                :title="wishlisted ? 'Remove from wishlist' : 'Add to wishlist'">
                <i :class="wishlisted ? 'fas fa-heart text-earth-rose' : 'far fa-heart text-gray-400'"></i>
            </button>
        @endauth
    </div>

    {{-- Product Info --}}
    <div class="p-5">
        <h3 class="font-display font-bold text-base mb-1.5 line-clamp-1" style="color: var(--on-surface);">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-earth-primary transition-colors">
                {{ $product->name }}
            </a>
        </h3>

        @if($product->category)
            <p class="text-xs mb-2" style="color: var(--on-surface-muted);">
                {{ ucfirst(str_replace('_', ' ', $product->category)) }}
            </p>
        @endif

        <p class="text-sm mb-3 line-clamp-2" style="color: var(--on-surface-muted);">
            {{ Str::limit(strip_tags($product->description), 80) }}
        </p>

        {{-- Color Swatches (for variant-based products) --}}
        @if($product->isPrintful && $product->relationLoaded('activeVariants') && $product->activeVariants->count() > 0)
            @php
                $colors = $product->activeVariants->whereNotNull('color_name')->unique('color_name')->take(6);
                $moreCount = $product->activeVariants->whereNotNull('color_name')->unique('color_name')->count() - 6;
            @endphp
            @if($colors->count() > 0)
                <div class="flex items-center gap-1.5 mb-3">
                    @foreach($colors as $variant)
                        <span class="w-5 h-5 rounded-full border border-gray-300/50 shadow-sm"
                              style="background-color: {{ $variant->color_hex ?? '#ccc' }};"
                              title="{{ $variant->color_name }}"></span>
                    @endforeach
                    @if($moreCount > 0)
                        <span class="text-xs" style="color: var(--on-surface-muted);">+{{ $moreCount }}</span>
                    @endif
                </div>
            @endif
        @endif

        {{-- Price --}}
        <div class="flex items-center gap-2 mb-4">
            @if($product->isPrintful && $product->price_range)
                <span class="text-lg font-bold" style="color: var(--on-surface);">{{ $product->price_range }}</span>
            @elseif($product->isOnSale)
                <span class="text-lg font-bold text-gradient-accent">${{ number_format($product->currentPrice, 2) }}</span>
                <span class="text-sm line-through" style="color: var(--on-surface-muted);">${{ number_format($product->price, 2) }}</span>
            @else
                <span class="text-lg font-bold" style="color: var(--on-surface);">${{ number_format($product->currentPrice, 2) }}</span>
            @endif
        </div>

        {{-- Add to Cart / Select Options --}}
        @if($product->isPrintful && $product->activeVariants->count() > 0)
            <a href="{{ route('products.show', $product->slug) }}" class="btn-gradient w-full text-sm py-2.5 text-center block">
                <i class="fas fa-palette mr-1"></i>Select Options
            </a>
        @elseif($product->isInStock)
            <form action="{{ route('cart.add') }}"
                  method="POST"
                  x-data
                  @submit.prevent="added = true; window.addToCartAjax($el); setTimeout(() => added = false, 2000)">
                @csrf
                <input type="hidden" name="item_type" value="product">
                <input type="hidden" name="item_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-gradient w-full text-sm py-2.5">
                    <template x-if="!added">
                        <span class="flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>Add to Cart
                        </span>
                    </template>
                    <template x-if="added">
                        <span class="flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>Added!
                        </span>
                    </template>
                </button>
            </form>
        @else
            <button disabled class="w-full py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed opacity-50" style="background: var(--surface-raised); color: var(--on-surface-muted);">
                Out of Stock
            </button>
        @endif
    </div>
</div>
