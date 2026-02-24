<x-app-layout>
    @section('title', 'Order Confirmation')
    @section('meta_description', 'Your order has been confirmed')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Success Icon --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-earth-success/10 mb-6">
                    <i class="fas fa-check-circle text-earth-success text-5xl"></i>
                </div>
                <h1 class="text-fluid-3xl font-display font-bold mb-2" style="color: var(--on-surface);">Order Confirmed!</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Thank you for your order</p>
            </div>

            {{-- Order Details Card --}}
            <div class="card-glass rounded-2xl p-8 mb-6" data-animate="fade-up">
                <div class="pb-4 mb-6" style="border-bottom: 1px solid var(--glass-border);">
                    <h2 class="text-xl font-display font-bold" style="color: var(--on-surface);">Order Details</h2>
                    <p class="mt-2" style="color: var(--on-surface-muted);">Order Number: <span class="font-semibold" style="color: var(--on-surface);">{{ $order->order_number }}</span></p>
                    <p style="color: var(--on-surface-muted);">Order Date: <span class="font-semibold" style="color: var(--on-surface);">{{ $order->created_at->format('F j, Y') }}</span></p>
                </div>

                {{-- Confirmation Email Notice --}}
                <div class="rounded-xl p-4 mb-6 bg-earth-green/10" style="border: 1px solid rgba(45, 106, 79, 0.2);">
                    <p class="text-earth-green">
                        <i class="fas fa-envelope mr-2"></i>
                        A confirmation email has been sent to <strong>{{ $order->customer->email }}</strong>
                    </p>
                </div>

                {{-- Order Items --}}
                <div class="mb-6">
                    <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Items Ordered</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-start pb-4" style="border-bottom: 1px solid var(--glass-border);">
                                <div>
                                    <h4 class="font-semibold" style="color: var(--on-surface);">{{ $item->name }}</h4>
                                    <p class="text-sm" style="color: var(--on-surface-muted);">Quantity: {{ $item->quantity }}</p>
                                    <p class="text-sm" style="color: var(--on-surface-muted);">Unit Price: ${{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <p class="font-semibold text-gradient">${{ number_format($item->total, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Total --}}
                <div class="pt-4" style="border-top: 1px solid var(--glass-border);">
                    <div class="flex justify-between mb-2">
                        <span style="color: var(--on-surface-muted);">Subtotal</span>
                        <span style="color: var(--on-surface);">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span style="color: var(--on-surface-muted);">Tax</span>
                        <span style="color: var(--on-surface);">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between mb-2">
                        <span style="color: var(--on-surface-muted);">
                            Discount
                            @if($order->coupon_code)
                                <span class="text-xs font-mono bg-earth-success/10 text-earth-success px-2 py-0.5 rounded-full ml-1">{{ $order->coupon_code }}</span>
                            @endif
                        </span>
                        <span class="text-earth-success">-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->loyalty_points_redeemed > 0)
                    <div class="flex justify-between mb-2">
                        <span style="color: var(--on-surface-muted);">
                            Loyalty Points
                            <span class="text-xs font-mono bg-earth-amber/10 text-earth-amber px-2 py-0.5 rounded-full ml-1">{{ number_format($order->loyalty_points_redeemed) }} pts</span>
                        </span>
                        <span class="text-earth-success">-${{ number_format($order->loyalty_discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold pt-2" style="border-top: 1px solid var(--glass-border);">
                        <span style="color: var(--on-surface);">Total</span>
                        <span class="text-gradient">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="card-glass rounded-2xl p-8 mb-6" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Shipping Address</h3>
                <div style="color: var(--on-surface-muted);" class="leading-relaxed">
                    <p style="color: var(--on-surface);" class="font-medium">{{ $order->customer->name }}</p>
                    <p>{{ $order->shipping_address['street'] }}</p>
                    <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['zip'] }}</p>
                    <p>{{ $order->shipping_address['country'] }}</p>
                </div>
            </div>

            {{-- Next Steps --}}
            <div class="card-glass rounded-2xl p-8 mb-8" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">What's Next?</h3>
                <ul class="space-y-3" style="color: var(--on-surface-muted);">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-earth-success mt-1"></i>
                        <span>We'll review your order and contact you regarding payment</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-earth-success mt-1"></i>
                        <span>You'll receive updates on your order status via email</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-earth-success mt-1"></i>
                        <span>For questions, contact us at {{ config('business.contact.phone') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center" data-animate="fade-up">
                <a href="{{ route('home') }}" class="btn-gradient btn-lg text-center">
                    Return to Home
                </a>
                @if(config('business.features.products'))
                <a href="{{ route('products.index') }}" class="btn-glass btn-lg text-center" style="color: var(--on-surface);">
                    Continue Shopping
                </a>
                @endif
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        {{-- GA4: purchase --}}
        @if(config('services.google.analytics_id'))
        gtag('event', 'purchase', {
            transaction_id: '{{ $order->order_number }}',
            value: {{ $order->total_amount }},
            currency: 'USD',
            tax: {{ $order->tax_amount }},
            @if($order->coupon_code)
            coupon: '{{ $order->coupon_code }}',
            @endif
            items: [
                @foreach($order->items as $item)
                {
                    item_id: '{{ $item->item?->sku ?? $item->item_id }}',
                    item_name: @json($item->name),
                    price: {{ $item->unit_price }},
                    quantity: {{ $item->quantity }},
                },
                @endforeach
            ]
        });
        @endif

        {{-- Meta Pixel: Purchase --}}
        @if(config('services.meta.pixel_id'))
        fbq('track', 'Purchase', {
            content_ids: [@foreach($order->items as $item)'{{ $item->item?->sku ?? $item->item_id }}',@endforeach],
            content_type: 'product',
            value: {{ $order->total_amount }},
            currency: 'USD',
            num_items: {{ $order->items->sum('quantity') }}
        });
        @endif
    </script>
    @endpush
</x-app-layout>
