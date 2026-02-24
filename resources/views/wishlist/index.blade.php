@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8" x-data="{ shareUrl: '', copied: false }">
        <h1 class="font-display text-fluid-3xl font-bold" style="color: var(--on-surface);">
            <i class="fas fa-heart text-earth-rose mr-2"></i>My Wishlist
        </h1>
        @if($wishlistItems->isNotEmpty())
        <div class="flex items-center gap-2">
            <button @click="
                fetch('{{ route('wishlist.share') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        shareUrl = data.url;
                        if (navigator.share) {
                            navigator.share({ title: 'My Wishlist', url: data.url });
                        } else {
                            navigator.clipboard.writeText(data.url);
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        }
                    })
            " class="btn-glass text-sm rounded-xl" style="color: var(--on-surface);">
                <i class="fas fa-share-alt mr-2"></i>
                <span x-show="!copied">Share Wishlist</span>
                <span x-show="copied" x-cloak><i class="fas fa-check mr-1"></i>Link Copied!</span>
            </button>
        </div>
        @endif
    </div>

    @if($wishlistItems->isEmpty())
        <div class="card-glass rounded-2xl p-12 text-center">
            <i class="far fa-heart text-5xl mb-4" style="color: var(--on-surface-muted);"></i>
            <h2 class="font-display text-xl font-semibold mb-2" style="color: var(--on-surface);">Your wishlist is empty</h2>
            <p class="mb-6" style="color: var(--on-surface-muted);">Browse our products to find something you love!</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('products.index') }}" class="btn-gradient text-sm py-2.5 px-6">Browse Products</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlistItems as $wishlistItem)
                @if($wishlistItem->item)
                    <div class="card-glass rounded-2xl overflow-hidden group">
                        {{-- Image --}}
                        <div class="relative overflow-hidden">
                            @php
                                $route = route('products.show', $wishlistItem->item->slug);
                            @endphp
                            <a href="{{ $route }}">
                                @if($wishlistItem->item->images && count($wishlistItem->item->images) > 0)
                                    <img src="{{ $wishlistItem->item->first_image_url }}" alt="{{ $wishlistItem->item->name }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center" style="background: linear-gradient(135deg, var(--surface-raised), var(--surface));">
                                        <i class="fas fa-box text-4xl" style="color: var(--on-surface-muted);"></i>
                                    </div>
                                @endif
                            </a>
                        </div>

                        {{-- Info --}}
                        <div class="p-5">
                            <h3 class="font-display font-bold text-base mb-1 line-clamp-1" style="color: var(--on-surface);">
                                <a href="{{ $route }}" class="hover:text-earth-primary transition-colors">{{ $wishlistItem->item->name }}</a>
                            </h3>
                            <p class="text-lg font-bold mb-4" style="color: var(--on-surface);">
                                ${{ number_format($wishlistItem->item->currentPrice, 2) }}
                            </p>

                            <div class="flex gap-2">
                                @if($wishlistItem->item->isInStock)
                                    <form action="{{ route('wishlist.move-to-cart', $wishlistItem) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="btn-gradient w-full text-sm py-2">
                                            <i class="fas fa-shopping-cart mr-1"></i>Move to Cart
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('wishlist.destroy', $wishlistItem) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 rounded-xl text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
