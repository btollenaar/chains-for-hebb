<x-app-layout>
    @section('title', 'Shopping Cart')

    <div class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Your Cart</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">Shopping Cart</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Review your items and proceed to checkout</p>
            </div>

            @if($cartItems && $cartItems->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Cart Items --}}
                    <div class="lg:col-span-2" data-animate="fade-up">
                        @foreach($cartItems as $item)
                            <div class="card-glass rounded-xl p-5 mb-4 flex flex-col sm:flex-row items-center gap-4">
                                {{-- Item Image --}}
                                <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden">
                                    @if($item->variant && $item->item && $item->item->isPrintful && $item->item->mockups && $item->item->mockups->count() > 0)
                                        <img src="{{ $item->item->mockups->where('is_primary', true)->first()?->mockup_url ?? $item->item->mockups->first()->mockup_url }}" alt="{{ $item->item->name }}" class="w-full h-full object-cover">
                                    @elseif($item->item && $item->item->images && count($item->item->images) > 0)
                                        <img src="{{ $item->item->first_image_url }}" alt="{{ $item->item->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-earth-primary/20 to-earth-green/20">
                                            <i class="fas fa-box text-earth-primary/40"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Item Details --}}
                                <div class="flex-1 text-center sm:text-left">
                                    <h3 class="text-lg font-display font-bold" style="color: var(--on-surface);">{{ $item->item->name }}</h3>
                                    @if($item->variant)
                                        <p class="text-xs" style="color: var(--on-surface-muted);">
                                            {{ $item->variant->display_name }}
                                        </p>
                                    @endif
                                    <p class="text-lg font-bold text-gradient mt-1">
                                        @if($item->variant)
                                            ${{ number_format($item->variant->retail_price, 2) }}
                                        @else
                                            ${{ number_format($item->item->currentPrice ?? $item->item->base_price, 2) }}
                                        @endif
                                    </p>
                                </div>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" onclick="this.parentElement.querySelector('input[name=quantity]').stepDown(); this.parentElement.submit();"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-earth-primary/10" style="background: var(--glass-bg); color: var(--on-surface);">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                               class="glass-input w-14 text-center rounded-lg text-sm" style="color: var(--on-surface);">
                                        <button type="button" onclick="this.parentElement.querySelector('input[name=quantity]').stepUp(); this.parentElement.submit();"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors hover:bg-earth-primary/10" style="background: var(--glass-bg); color: var(--on-surface);">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </form>

                                    {{-- Remove Button --}}
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-500/10 transition-colors">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        {{-- Clear Cart --}}
                        <form action="{{ route('cart.clear') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-600 text-sm font-medium transition-colors">
                                <i class="fas fa-times-circle mr-1"></i>Clear Cart
                            </button>
                        </form>
                    </div>

                    {{-- Order Summary --}}
                    <div class="lg:col-span-1" data-animate="fade-up">
                        <div class="card-glass rounded-2xl p-6 sticky top-24">
                            <h2 class="text-xl font-display font-bold mb-4" style="color: var(--on-surface);">Order Summary</h2>

                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between">
                                    <span style="color: var(--on-surface-muted);">Subtotal</span>
                                    <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--on-surface-muted);">Tax ({{ config('business.payments.tax_rate') * 100 }}%)</span>
                                    <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="pt-3 flex justify-between text-lg" style="border-top: 1px solid var(--glass-border);">
                                    <span class="font-bold" style="color: var(--on-surface);">Total</span>
                                    <span class="font-bold text-gradient">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.index') }}" class="btn-gradient btn-lg w-full text-center block">
                                Proceed to Checkout
                            </a>

                            @guest
                                <p class="text-xs text-center mt-3" style="color: var(--on-surface-muted);">
                                    Already have an account? <a href="{{ route('login') }}" class="text-earth-primary hover:opacity-80">Sign in</a>
                                </p>
                            @endguest

                            <a href="{{ route('products.index') }}" class="block w-full text-center text-earth-primary hover:opacity-80 text-sm mt-4 transition-opacity">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-16" data-animate="fade-up">
                    <div class="w-20 h-20 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-3xl text-earth-primary/50"></i>
                    </div>
                    <h2 class="text-2xl font-display font-bold mb-3" style="color: var(--on-surface);">Your cart is empty</h2>
                    <p class="mb-8" style="color: var(--on-surface-muted);">Add some products or services to get started</p>
                    <div class="flex justify-center gap-3">
                        @if(config('business.features.products'))
                        <a href="{{ route('products.index') }}" class="btn-gradient">
                            Browse Products
                        </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
