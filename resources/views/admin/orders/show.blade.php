@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.orders.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
                <a href="{{ route('admin.orders.invoice', $order) }}" class="btn-admin-secondary inline-flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Download Invoice
                </a>
                <a href="{{ route('admin.orders.edit', $order) }}" class="btn-admin-primary">
                    <i class="fas fa-edit mr-2"></i>Edit Order
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Order Number</p>
                                    <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Order Date</p>
                                    <p class="font-semibold text-gray-900">{{ $order->created_at->format('F j, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Status</p>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-blue-100 text-blue-800',
                                        ];
                                        $color = $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Fulfillment Status</p>
                                    @php
                                        $fulfillmentColors = [
                                            'pending' => 'bg-gray-100 text-gray-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-cyan-100 text-cyan-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $color = $fulfillmentColors[$order->fulfillment_status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($order->fulfillment_status) }}
                                    </span>
                                </div>
                                @if($order->payment_method)
                                <div>
                                    <p class="text-sm text-gray-500">Payment Method</p>
                                    <p class="font-semibold text-gray-900">{{ ucfirst($order->payment_method) }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Information -->
                    @if($order->tracking_number || $order->shipped_at)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-truck mr-2 text-admin-teal"></i>Tracking Information
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                @if($order->tracking_carrier)
                                <div>
                                    <p class="text-sm text-gray-500">Carrier</p>
                                    <p class="font-semibold text-gray-900">{{ strtoupper($order->tracking_carrier) }}</p>
                                </div>
                                @endif
                                @if($order->tracking_number)
                                <div>
                                    <p class="text-sm text-gray-500">Tracking Number</p>
                                    <p class="font-mono font-semibold text-gray-900">{{ $order->tracking_number }}</p>
                                </div>
                                @endif
                                @if($order->shipped_at)
                                <div>
                                    <p class="text-sm text-gray-500">Shipped</p>
                                    <p class="font-semibold text-gray-900">{{ $order->shipped_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @endif
                                @if($order->delivered_at)
                                <div>
                                    <p class="text-sm text-gray-500">Delivered</p>
                                    <p class="font-semibold text-gray-900">{{ $order->delivered_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @endif
                            </div>
                            @if($order->tracking_url)
                                <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener" class="inline-block mt-3 btn-admin-primary btn-admin-sm">
                                    <i class="fas fa-external-link-alt mr-1"></i>Track Package
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Order Items -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Order Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                                    @if($item->description)
                                                        <div class="text-sm text-gray-500">{{ Str::limit($item->description, 60) }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-xs text-gray-500">
                                                        {{ $item->item_type == 'App\\Models\\Product' ? 'Product' : 'Service' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                                    ${{ number_format($item->unit_price, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                                    ${{ number_format($item->total, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Order Totals -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Order Totals</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax</span>
                                    <span class="font-semibold">${{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">
                                        Discount
                                        @if($order->coupon_code)
                                            <span class="text-xs font-mono bg-green-100 text-green-800 px-2 py-0.5 rounded-full ml-1">{{ $order->coupon_code }}</span>
                                        @endif
                                    </span>
                                    <span class="text-green-600 font-semibold">-${{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                                @endif
                                <div class="border-t pt-2 flex justify-between text-lg">
                                    <span class="font-bold">Total</span>
                                    <span class="font-bold text-brand-color">${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($order->notes || $order->admin_notes)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Notes</h3>

                            @if($order->notes)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-1">Customer Notes:</p>
                                <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $order->notes }}</p>
                            </div>
                            @endif

                            @if($order->admin_notes)
                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-1">Admin Notes:</p>
                                <p class="text-sm text-gray-600 bg-yellow-50 p-3 rounded border border-yellow-200">{{ $order->admin_notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar Column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Customer Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-semibold text-gray-900">{{ $order->customer->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <a href="mailto:{{ $order->customer->email }}" class="link-admin-info">
                                        {{ $order->customer->email }}
                                    </a>
                                </div>
                                @if($order->customer->phone)
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <a href="tel:{{ $order->customer->phone }}" class="link-admin-info">
                                        {{ $order->customer->phone }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Shipping Address</h3>
                            <div class="text-sm text-gray-700 leading-relaxed">
                                {{ $order->shipping_address['street'] }}<br>
                                {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['zip'] }}<br>
                                {{ $order->shipping_address['country'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Billing Address</h3>
                            @if($order->billing_address == $order->shipping_address)
                                <p class="text-sm text-gray-500 italic">Same as shipping address</p>
                            @else
                                <div class="text-sm text-gray-700 leading-relaxed">
                                    {{ $order->billing_address['street'] }}<br>
                                    {{ $order->billing_address['city'] }}, {{ $order->billing_address['state'] }} {{ $order->billing_address['zip'] }}<br>
                                    {{ $order->billing_address['country'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
