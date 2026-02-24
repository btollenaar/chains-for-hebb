<x-app-layout>
    @section('title', $ownerName . "'s Wishlist")

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Shared Wishlist</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">
                    <i class="fas fa-heart text-earth-rose mr-2"></i>{{ $ownerName }}'s Wishlist
                </h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">{{ $wishlistItems->count() }} {{ Str::plural('item', $wishlistItems->count()) }}</p>
            </div>

            @if($wishlistItems->isEmpty())
                <div class="card-glass rounded-2xl p-12 text-center" data-animate="fade-up">
                    <i class="far fa-heart text-5xl mb-4" style="color: var(--on-surface-muted);"></i>
                    <h2 class="font-display text-xl font-semibold mb-2" style="color: var(--on-surface);">This wishlist is empty</h2>
                    <p class="mb-6" style="color: var(--on-surface-muted);">No items have been added yet.</p>
                    <a href="{{ route('products.index') }}" class="btn-gradient text-sm py-2.5 px-6">Browse Products</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" data-animate="stagger">
                    @foreach($wishlistItems as $wishlistItem)
                        @if($wishlistItem->item)
                            <div class="card-glass rounded-2xl overflow-hidden group">
                                <div class="relative overflow-hidden">
                                    <a href="{{ route('products.show', $wishlistItem->item->slug) }}">
                                        @if($wishlistItem->item->images && count($wishlistItem->item->images) > 0)
                                            <img src="{{ $wishlistItem->item->first_image_url }}" alt="{{ $wishlistItem->item->name }}"
                                                class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                        @else
                                            <div class="w-full h-48 flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                                                <i class="fas fa-box text-4xl" style="color: var(--on-surface-muted);"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                                <div class="p-5">
                                    <h3 class="font-display font-bold text-base mb-1 line-clamp-1" style="color: var(--on-surface);">
                                        <a href="{{ route('products.show', $wishlistItem->item->slug) }}" class="hover:text-earth-primary transition-colors">{{ $wishlistItem->item->name }}</a>
                                    </h3>
                                    <p class="text-lg font-bold mb-4" style="color: var(--on-surface);">
                                        ${{ number_format($wishlistItem->item->currentPrice, 2) }}
                                    </p>
                                    @if($wishlistItem->item->isInStock)
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="item_type" value="product">
                                            <input type="hidden" name="item_id" value="{{ $wishlistItem->item->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-gradient w-full text-sm py-2">
                                                <i class="fas fa-shopping-cart mr-1"></i>Add to Cart
                                            </button>
                                        </form>
                                    @else
                                        <span class="block text-center text-sm py-2" style="color: var(--on-surface-muted);">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
