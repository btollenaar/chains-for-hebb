<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Header with Profile Button -->
            <div class="mb-6">
                <div class="text-center mb-8">
                    <h1 class="page-heading text-5xl font-bold text-[#2E2A25] mb-6">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-xl text-gray-600 mb-4">Here's what's happening with your account</p>
                </div>

                <!-- Profile Button -->
                <div class="text-center">
                    <a href="{{ route('profile.edit') }}"
                       class="inline-flex items-center px-6 py-3 deep-teal-background-color hover:opacity-90 text-white font-semibold rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-user-circle mr-2 text-xl"></i>
                        <span>View My Profile</span>
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8">
                <!-- Total Orders -->
                <a href="{{ route('orders.index') }}" class="block h-full">
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all cursor-pointer h-full flex flex-col">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                                <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                            </div>
                        </div>
                        @if($stats['total_spent'] > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-500">Total Spent</p>
                            <p class="text-lg font-semibold text-green-600">${{ number_format($stats['total_spent'], 2) }}</p>
                        </div>
                        @endif
                    </div>
                </a>

                <!-- Account Info -->
                <a href="{{ route('profile.edit') }}" class="block h-full">
                    <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all cursor-pointer h-full flex flex-col">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <i class="fas fa-user-circle text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">Account Status</p>
                            <p class="text-2xl font-bold text-gray-900">Active</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500">Member Since</p>
                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</p>
                    </div>
                    </div>
                </a>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-3 py-1.5 deep-teal-background-color hover:opacity-90 text-white text-sm font-medium rounded transition-colors">
                            View All <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @if($recentOrders->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-bag text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500 mb-4">No orders yet</p>
                            <a href="{{ route('products.index') }}" class="inline-block bg-abs-primary hover:bg-gray-700 text-white px-4 py-2 rounded transition-colors">
                                <i class="fas fa-shopping-cart mr-2"></i>Shop Products
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                                <a href="{{ route('orders.show', $order) }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="font-semibold text-gray-900">
                                            Order #{{ $order->order_number }}
                                        </div>
                                        <div class="text-lg font-bold text-gray-900">
                                            ${{ number_format($order->total_amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $order->created_at->format('M j, Y') }}
                                        </span>
                                        @php
                                            $paymentColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                    @if($order->items->count() > 0)
                                    <div class="mt-2 text-xs text-gray-500">
                                        {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}
                                    </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 deep-teal-background-color hover:opacity-90 text-white text-sm font-medium rounded-lg transition-colors">
                                View All Orders <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                    <a href="{{ route('products.index') }}" class="flex items-center justify-center p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        <span class="font-medium">Shop Products</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center justify-center p-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-box mr-2"></i>
                        <span class="font-medium">My Orders</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-center p-4 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-user-cog mr-2"></i>
                        <span class="font-medium">Edit Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
