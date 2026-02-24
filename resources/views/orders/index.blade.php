<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="text-center mb-6">
                <h1 class="page-heading text-5xl font-bold text-[#2E2A25] mb-6">My Orders</h1>
                <p class="text-xl text-gray-600">View and track your order history</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter mr-1"></i>Filter by Status
                        </label>
                        <select name="status" id="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-abs-primary focus:ring focus:ring-abs-primary focus:ring-opacity-50">
                            <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Orders</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i>Search Order Number
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Enter order number..."
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-abs-primary focus:ring focus:ring-abs-primary focus:ring-opacity-50">
                    </div>

                    <!-- Submit -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-abs-primary hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['status', 'search']))
                        <a href="{{ route('orders.index') }}" class="ml-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors whitespace-nowrap">
                            Clear
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Orders List -->
            @if($orders->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-6"></i>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">No Orders Found</h2>
                    <p class="text-gray-600 mb-6">
                        @if(request()->hasAny(['status', 'search']))
                            No orders match your filters. Try adjusting your search criteria.
                        @else
                            You haven't placed any orders yet. Start shopping today!
                        @endif
                    </p>
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('products.index') }}" class="bg-abs-primary hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>Shop Products
                        </a>
                    </div>
                </div>
            @else
                <!-- Desktop Table View -->
                <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Items
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment Status
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fulfillment
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $order->order_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->created_at->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $order->items->count() }} items</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $paymentColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'refunded' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $fulfillmentColors = [
                                                'pending' => 'bg-gray-100 text-gray-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $fulfillmentColors[$order->fulfillment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($order->fulfillment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('orders.show', $order) }}" class="text-abs-primary hover:opacity-80">
                                            View Details <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="block bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <div class="font-semibold text-gray-900">
                                    Order #{{ $order->order_number }}
                                </div>
                                <div class="text-lg font-bold text-gray-900">
                                    ${{ number_format($order->total_amount, 2) }}
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">
                                        <i class="fas fa-calendar mr-2"></i>{{ $order->created_at->format('M j, Y') }}
                                    </span>
                                    <span class="text-gray-600">{{ $order->items->count() }} items</span>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                    <span class="text-gray-600">Payment:</span>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Fulfillment:</span>
                                    @php
                                        $fulfillmentColors = [
                                            'pending' => 'bg-gray-100 text-gray-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $fulfillmentColors[$order->fulfillment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->fulfillment_status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 text-abs-primary text-sm font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
