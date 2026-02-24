<x-app-layout>
    @section('title', 'Track Order #' . $order->order_number)

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="text-center mb-8" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Package Tracking</p>
                <h1 class="text-fluid-2xl font-display font-bold mb-2" style="color: var(--on-surface);">Order #{{ $order->order_number }}</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Placed {{ $order->created_at->format('F j, Y') }}</p>
            </div>

            {{-- Order Timeline --}}
            <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-6" style="color: var(--on-surface);">Order Status</h3>
                <x-order-timeline :order="$order" />
            </div>

            {{-- Tracking Details --}}
            @if($order->tracking_number)
            <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Tracking Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 rounded-xl" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Tracking Number</p>
                            <p class="font-mono font-semibold text-lg" style="color: var(--on-surface);">{{ $order->tracking_number }}</p>
                        </div>
                        @if($order->tracking_carrier)
                        <div class="text-right">
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Carrier</p>
                            <p class="font-semibold" style="color: var(--on-surface);">
                                @php
                                    $carrierIcons = [
                                        'USPS' => 'fa-mail-bulk',
                                        'UPS' => 'fa-box',
                                        'FedEx' => 'fa-shipping-fast',
                                        'DHL' => 'fa-globe',
                                    ];
                                @endphp
                                <i class="fas {{ $carrierIcons[$order->tracking_carrier] ?? 'fa-truck' }} mr-1"></i>
                                {{ $order->tracking_carrier }}
                            </p>
                        </div>
                        @endif
                    </div>

                    @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-center w-full btn-gradient text-base py-3 rounded-xl">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Track on {{ $order->tracking_carrier ?? 'Carrier' }}
                    </a>
                    @endif

                    @if($order->shipped_at)
                    <p class="text-sm text-center" style="color: var(--on-surface-muted);">
                        Shipped {{ $order->shipped_at->format('F j, Y \a\t g:i A') }}
                    </p>
                    @endif
                    @if($order->delivered_at)
                    <p class="text-sm text-center text-earth-success font-semibold">
                        <i class="fas fa-check-circle mr-1"></i>Delivered {{ $order->delivered_at->format('F j, Y \a\t g:i A') }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Shipping Address --}}
            <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Shipping Address</h3>
                <div class="text-sm leading-relaxed" style="color: var(--on-surface-muted);">
                    <p class="font-medium mb-1" style="color: var(--on-surface);">{{ $order->shipping_address['street'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['zip'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center" data-animate="fade-up">
                @auth
                <a href="{{ route('orders.show', $order) }}" class="btn-glass text-sm rounded-xl text-center" style="color: var(--on-surface);">
                    <i class="fas fa-file-alt mr-2"></i>Full Order Details
                </a>
                <a href="{{ route('orders.index') }}" class="btn-glass text-sm rounded-xl text-center" style="color: var(--on-surface);">
                    <i class="fas fa-list mr-2"></i>All Orders
                </a>
                @endauth
                <a href="{{ route('products.index') }}" class="btn-glass text-sm rounded-xl text-center" style="color: var(--on-surface);">
                    <i class="fas fa-shopping-cart mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
