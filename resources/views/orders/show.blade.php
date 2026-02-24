<x-app-layout>
    @section('title', 'Order #' . $order->order_number)

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8" data-animate="fade-up">
                <div class="text-center mb-6">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Order</p>
                    <h1 class="text-fluid-2xl font-display font-bold mb-2" style="color: var(--on-surface);">Order #{{ $order->order_number }}</h1>
                    <p class="text-lg" style="color: var(--on-surface-muted);">Placed {{ $order->created_at->format('F j, Y') }}</p>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('orders.index') }}" class="btn-glass text-sm" style="color: var(--on-surface);">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div data-flash-success="{{ session('success') }}" style="display: none;"></div>
            @endif

            {{-- Order Timeline --}}
            <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                <h3 class="text-lg font-display font-bold mb-6" style="color: var(--on-surface);">Order Status</h3>
                <x-order-timeline :order="$order" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Column --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Order Summary --}}
                    <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-6" style="color: var(--on-surface);">Order Summary</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Order Number</p>
                                <p class="font-semibold" style="color: var(--on-surface);">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Order Date</p>
                                <p class="font-semibold" style="color: var(--on-surface);">{{ $order->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Payment Status</p>
                                @php
                                    $paymentStyles = [
                                        'pending' => 'bg-yellow-500/10 text-yellow-600',
                                        'paid' => 'bg-earth-success/10 text-earth-success',
                                        'failed' => 'bg-red-500/10 text-red-500',
                                        'refunded' => 'bg-earth-green/10 text-earth-green',
                                    ];
                                    $payStyle = $paymentStyles[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $payStyle }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Fulfillment Status</p>
                                @php
                                    $fulfillStyles = [
                                        'pending' => 'bg-yellow-500/10 text-yellow-600',
                                        'processing' => 'bg-earth-green/10 text-earth-green',
                                        'shipped' => 'bg-earth-sage/10 text-earth-sage',
                                        'delivered' => 'bg-earth-success/10 text-earth-success',
                                        'completed' => 'bg-earth-success/10 text-earth-success',
                                        'cancelled' => 'bg-red-500/10 text-red-500',
                                    ];
                                    $fulStyle = $fulfillStyles[$order->fulfillment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $fulStyle }}">
                                    {{ ucfirst($order->fulfillment_status) }}
                                </span>
                            </div>
                            @if($order->payment_method)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Payment Method</p>
                                <p class="font-semibold" style="color: var(--on-surface);">{{ ucfirst($order->payment_method) }}</p>
                            </div>
                            @endif
                            @if($order->coupon_code)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Coupon Used</p>
                                <p class="font-semibold text-earth-success">{{ $order->coupon_code }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Order Items</h3>

                        {{-- Desktop Table --}}
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr style="border-bottom: 1px solid var(--glass-border);">
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--on-surface-muted);">Item</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--on-surface-muted);">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--on-surface-muted);">Price</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--on-surface-muted);">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr style="border-bottom: 1px solid var(--glass-border);">
                                            <td class="px-4 py-4">
                                                <p class="font-semibold text-sm" style="color: var(--on-surface);">{{ $item->name }}</p>
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-earth-primary/10 text-earth-primary">
                                                    {{ $item->item_type == 'App\\Models\\Product' ? 'Product' : 'Service' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center text-sm" style="color: var(--on-surface);">{{ $item->quantity }}</td>
                                            <td class="px-4 py-4 text-right text-sm" style="color: var(--on-surface);">${{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-4 py-4 text-right text-sm font-semibold" style="color: var(--on-surface);">${{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div class="sm:hidden space-y-3">
                            @foreach($order->items as $item)
                                <div class="p-4 rounded-xl" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-semibold text-sm" style="color: var(--on-surface);">{{ $item->name }}</p>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-earth-primary/10 text-earth-primary">
                                            {{ $item->item_type == 'App\\Models\\Product' ? 'Product' : 'Service' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm" style="color: var(--on-surface-muted);">
                                        <span>{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</span>
                                        <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($item->total, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Order Totals --}}
                    <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Order Totals</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span style="color: var(--on-surface-muted);">Subtotal</span>
                                <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span style="color: var(--on-surface-muted);">Tax</span>
                                <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between">
                                <span style="color: var(--on-surface-muted);">Discount</span>
                                <span class="text-earth-success font-semibold">-${{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="pt-2 flex justify-between text-lg" style="border-top: 1px solid var(--glass-border);">
                                <span class="font-bold" style="color: var(--on-surface);">Total</span>
                                <span class="font-bold text-gradient">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Customer Notes --}}
                    @if($order->notes)
                    <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Order Notes</h3>
                        <div class="text-sm rounded-xl p-4" style="color: var(--on-surface-muted); background: var(--glass-bg); border: 1px solid var(--glass-border);">
                            {{ $order->notes }}
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- Contact Information --}}
                    <div class="card-glass rounded-2xl p-6" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Contact Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Name</p>
                                <p class="font-semibold" style="color: var(--on-surface);">{{ $order->customer->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Email</p>
                                <a href="mailto:{{ $order->customer->email }}" class="text-earth-primary hover:opacity-80 text-sm transition-opacity">
                                    {{ $order->customer->email }}
                                </a>
                            </div>
                            @if($order->customer->phone)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Phone</p>
                                <a href="tel:{{ $order->customer->phone }}" class="text-earth-primary hover:opacity-80 text-sm transition-opacity">
                                    {{ $order->customer->phone }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Shipping Address --}}
                    <div class="card-glass rounded-2xl p-6" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Shipping Address</h3>
                        <div class="text-sm leading-relaxed" style="color: var(--on-surface-muted);">
                            <p class="font-medium mb-1" style="color: var(--on-surface);">{{ $order->shipping_address['street'] }}</p>
                            <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['zip'] }}</p>
                            <p>{{ $order->shipping_address['country'] }}</p>
                        </div>
                    </div>

                    {{-- Billing Address --}}
                    <div class="card-glass rounded-2xl p-6" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Billing Address</h3>
                        @if($order->billing_address == $order->shipping_address)
                            <p class="text-sm italic" style="color: var(--on-surface-muted);">Same as shipping address</p>
                        @else
                            <div class="text-sm leading-relaxed" style="color: var(--on-surface-muted);">
                                <p class="font-medium mb-1" style="color: var(--on-surface);">{{ $order->billing_address['street'] }}</p>
                                <p>{{ $order->billing_address['city'] }}, {{ $order->billing_address['state'] }} {{ $order->billing_address['zip'] }}</p>
                                <p>{{ $order->billing_address['country'] }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="card-glass rounded-2xl p-6" data-animate="fade-up">
                        <h3 class="text-lg font-display font-bold mb-4" style="color: var(--on-surface);">Actions</h3>
                        <div class="space-y-3">
                            @if($order->tracking_number)
                            <a href="{{ route('orders.tracking', $order) }}"
                               class="flex items-center justify-center w-full btn-gradient text-sm rounded-xl">
                                <i class="fas fa-truck mr-2"></i>Track Package
                            </a>
                            @endif
                            @if($order->payment_status === 'paid')
                            <a href="{{ route('orders.invoice', $order) }}"
                               class="flex items-center justify-center w-full btn-glass text-sm rounded-xl" style="color: var(--on-surface);">
                                <i class="fas fa-file-pdf mr-2"></i>Download Invoice
                            </a>
                            <form action="{{ route('orders.reorder', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center justify-center w-full btn-glass text-sm rounded-xl" style="color: var(--on-surface);">
                                    <i class="fas fa-redo mr-2"></i>Reorder These Items
                                </button>
                            </form>
                            @endif
                            @if(in_array($order->fulfillment_status, ['delivered', 'completed']) && !$order->returnRequests()->whereIn('status', ['requested', 'approved'])->exists())
                            <a href="{{ route('returns.create', $order) }}"
                               class="flex items-center justify-center w-full btn-glass text-sm rounded-xl" style="color: var(--on-surface);">
                                <i class="fas fa-undo mr-2"></i>Request Return
                            </a>
                            @endif
                            <a href="{{ route('orders.index') }}"
                               class="flex items-center justify-center w-full btn-glass text-sm rounded-xl" style="color: var(--on-surface);">
                                <i class="fas fa-list mr-2"></i>View All Orders
                            </a>
                            <a href="{{ route('products.index') }}"
                               class="block w-full text-center text-earth-primary hover:opacity-80 text-sm font-medium transition-opacity mt-2">
                                <i class="fas fa-shopping-cart mr-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>

                    {{-- Help Card --}}
                    <div class="card-glass rounded-2xl p-6 bg-earth-green/5" data-animate="fade-up">
                        <h4 class="font-display font-bold text-earth-green mb-2">Need Help?</h4>
                        <p class="text-sm mb-3" style="color: var(--on-surface-muted);">
                            Questions about your order? Contact us at
                            <a href="tel:{{ \App\Models\Setting::get('contact.phone', config('business.contact.phone')) }}" class="font-semibold text-earth-primary hover:opacity-80 transition-opacity">{{ \App\Models\Setting::get('contact.phone', config('business.contact.phone')) }}</a>
                            or email
                            <a href="mailto:{{ \App\Models\Setting::get('contact.email', config('business.contact.email')) }}" class="font-semibold text-earth-primary hover:opacity-80 transition-opacity">{{ \App\Models\Setting::get('contact.email', config('business.contact.email')) }}</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
