@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
        <p class="text-gray-600 mt-1">View and manage customer orders and fulfillment</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards: Mobile-optimized (2 cols mobile, 3 cols tablet, 5 cols desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
                <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-shopping-cart text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index', ['payment_status' => 'pending']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl md:text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['pending']) }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-clock text-yellow-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index', ['payment_status' => 'paid']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Paid</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['paid']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index', ['payment_status' => 'failed']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Failed</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['failed']) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index', ['payment_status' => 'refunded']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Refunded</p>
                            <p class="text-2xl md:text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['refunded']) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-undo text-purple-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Filters Section - Desktop Only -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Order # or customer name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                        </div>

                        <!-- Payment Status Filter -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select name="payment_status" id="payment_status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Payment Statuses</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        <!-- Fulfillment Status Filter -->
                        <div>
                            <label for="fulfillment_status" class="block text-sm font-medium text-gray-700 mb-2">Fulfillment Status</label>
                            <select name="fulfillment_status" id="fulfillment_status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Fulfillment Statuses</option>
                                <option value="pending" {{ request('fulfillment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('fulfillment_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('fulfillment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('fulfillment_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Filter Buttons (Full Width on Next Row) -->
                        <div class="md:col-span-3 flex gap-2">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn-admin-secondary">
                                Clear
                            </a>
                            <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn-admin-success">
                                <i class="fas fa-download mr-2"></i>Export CSV
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.orders.index') }}">
                <!-- Search -->
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Order # or customer name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Payment Status Filter -->
                <div>
                    <label for="mobile-payment-status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select name="payment_status" id="mobile-payment-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Payment Statuses</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Fulfillment Status Filter -->
                <div>
                    <label for="mobile-fulfillment-status" class="block text-sm font-medium text-gray-700 mb-2">Fulfillment Status</label>
                    <select name="fulfillment_status" id="mobile-fulfillment-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Fulfillment Statuses</option>
                        <option value="pending" {{ request('fulfillment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('fulfillment_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('fulfillment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('fulfillment_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Orders Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($orders->count() > 0)
                        <!-- Mobile Cards View - Visible only on mobile -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($orders as $order)
                                <x-admin.table-card
                                    :item="$order"
                                    route="admin.orders.show"
                                    :fields="[
                                        [
                                            'label' => 'Order Number',
                                            'render' => function($item) {
                                                return '<span class=\'font-semibold text-abs-primary\'>#' . e($item->order_number) . '</span>';
                                            }
                                        ],
                                        [
                                            'label' => 'Customer',
                                            'render' => function($item) {
                                                return '<div class=\'font-medium text-gray-900\'>' . e($item->customer->name) . '</div>' .
                                                       '<div class=\'text-sm text-gray-500\'>' . e($item->customer->email) . '</div>';
                                            }
                                        ],
                                        [
                                            'label' => 'Total',
                                            'render' => function($item) {
                                                return '<span class=\'text-lg font-semibold text-gray-900\'>$' . number_format($item->total_amount, 2) . '</span>';
                                            }
                                        ],
                                        [
                                            'label' => 'Payment Status',
                                            'render' => function($item) {
                                                $paymentColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'paid' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    'refunded' => 'bg-blue-100 text-blue-800',
                                                ];
                                                $color = $paymentColors[$item->payment_status] ?? 'bg-gray-100 text-gray-800';
                                                return '<span class=\'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $color . '\'>' . ucfirst($item->payment_status) . '</span>';
                                            }
                                        ]
                                    ]"
                                    :actions="[
                                        ['route' => 'admin.orders.show', 'icon' => 'fa-eye', 'color' => 'blue', 'label' => 'View order'],
                                        ['route' => 'admin.orders.edit', 'icon' => 'fa-edit', 'color' => 'indigo', 'label' => 'Edit order']
                                    ]"
                                />
                            @endforeach
                        </div>

                        <!-- Desktop Table - Hidden on mobile -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fulfillment</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-abs-primary font-semibold">
                                                    {{ $order->order_number }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $order->customer->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $order->customer->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                ${{ number_format($order->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $fulfillmentColors = [
                                                        'pending' => 'bg-gray-100 text-gray-800',
                                                        'processing' => 'bg-blue-100 text-blue-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $color = $fulfillmentColors[$order->fulfillment_status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                    {{ ucfirst($order->fulfillment_status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.orders.show', $order) }}"
                                                   aria-label="View order details"
                                                   class="text-abs-primary hover:opacity-80 mr-3">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </a>
                                                <a href="{{ route('admin.orders.edit', $order) }}"
                                                   aria-label="Edit order"
                                                   class="link-admin-info">
                                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $orders->appends(request()->query())->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <i class="fas fa-shopping-bag text-gray-400 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                            <p class="text-gray-500">Orders from customers will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
