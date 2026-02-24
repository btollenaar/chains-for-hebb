@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $customer->name }}</h1>
                <p class="text-gray-600 mt-2">Customer details and activity history</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.customers.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Customer Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Customer Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Contact Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Contact Details</h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Email:</span>
                                    <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-800 ml-2">{{ $customer->email }}</a>
                                </div>
                                @if($customer->phone)
                                    <div>
                                        <span class="text-gray-600">Phone:</span>
                                        <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:text-blue-800 ml-2">{{ $customer->phone }}</a>
                                    </div>
                                @endif
                                @if($customer->address)
                                    <div>
                                        <span class="text-gray-600">Address:</span>
                                        <span class="text-gray-900 ml-2">{{ $customer->address }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Account Details</h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Role:</span>
                                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                                        {{ ucfirst($customer->role ?? 'customer') }}
                                    </span>
                                </div>
                                @if($customer->is_admin)
                                    <div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                            <i class="fas fa-shield-alt mr-1"></i>Administrator
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-gray-600">Joined:</span>
                                    <span class="text-gray-900 ml-2">{{ $customer->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Email Verified:</span>
                                    @if($customer->email_verified_at)
                                        <span class="ml-2 text-green-600">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @else
                                        <span class="ml-2 text-yellow-600">
                                            <i class="fas fa-exclamation-circle"></i> Not Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Activity Statistics -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Activity Summary</h4>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Orders:</span>
                                    <span class="text-gray-900 ml-2 font-semibold">{{ $stats['total_orders'] }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total Spent:</span>
                                    <span class="text-green-600 ml-2 font-semibold">${{ number_format($stats['total_spent'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Tags -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                 x-data="{
                     tags: @js($tags->map(fn($tag) => [
                         'id' => $tag->id,
                         'name' => $tag->name,
                         'slug' => $tag->slug,
                         'color' => $tag->color,
                         'assigned' => $customer->tags->contains('id', $tag->id),
                     ])),
                     loading: null,
                     async toggleTag(tag) {
                         this.loading = tag.id;
                         try {
                             const response = await fetch('{{ route('admin.tags.assign') }}', {
                                 method: 'POST',
                                 headers: {
                                     'Content-Type': 'application/json',
                                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                     'Accept': 'application/json',
                                 },
                                 body: JSON.stringify({
                                     customer_id: {{ $customer->id }},
                                     tag_id: tag.id,
                                 }),
                             });
                             const data = await response.json();
                             if (data.success) {
                                 tag.assigned = data.action === 'assigned';
                             }
                         } catch (error) {
                             // Silently handle error
                         } finally {
                             this.loading = null;
                         }
                     }
                 }">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                        <i class="fas fa-tags text-admin-teal mr-2"></i>Customer Tags
                    </h3>

                    <!-- Current Tags -->
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Tags</p>
                        <div class="flex flex-wrap gap-2 min-h-[2rem]">
                            <template x-for="tag in tags.filter(t => t.assigned)" :key="tag.id">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium text-white"
                                      :style="'background-color: ' + tag.color">
                                    <span x-text="tag.name"></span>
                                    <button @click="toggleTag(tag)"
                                            class="ml-1 hover:opacity-75 focus:outline-none"
                                            :disabled="loading === tag.id"
                                            aria-label="Remove tag">
                                        <i class="fas fa-times text-xs" x-show="loading !== tag.id"></i>
                                        <i class="fas fa-spinner fa-spin text-xs" x-show="loading === tag.id"></i>
                                    </button>
                                </span>
                            </template>
                            <span x-show="tags.filter(t => t.assigned).length === 0"
                                  class="text-sm text-gray-400 italic">No tags assigned</span>
                        </div>
                    </div>

                    <!-- Available Tags (Checkboxes) -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Manage Tags</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            <template x-for="tag in tags" :key="tag.id">
                                <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-colors hover:bg-gray-50"
                                       :class="tag.assigned ? 'border-gray-400 bg-gray-50' : 'border-gray-200'">
                                    <input type="checkbox"
                                           :checked="tag.assigned"
                                           :disabled="loading === tag.id"
                                           @change="toggleTag(tag)"
                                           class="rounded border-gray-300 text-admin-teal focus:ring-admin-teal">
                                    <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                          :style="'background-color: ' + tag.color"></span>
                                    <span class="text-sm text-gray-700 truncate" x-text="tag.name"></span>
                                    <i class="fas fa-spinner fa-spin text-xs text-gray-400 ml-auto"
                                       x-show="loading === tag.id"></i>
                                </label>
                            </template>
                        </div>
                        @if($tags->isEmpty())
                            <p class="text-sm text-gray-500">
                                No tags have been created yet.
                                <a href="{{ route('admin.tags.create') }}" class="text-admin-teal hover:underline">Create a tag</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <i class="fas fa-shopping-cart text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Spent</p>
                            <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_spent'], 2) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
                        @if($customer->orders->count() > 0)
                            <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="btn-admin-primary btn-admin-sm">
                                View All <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($customer->orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($customer->orders as $order)
                                <div class="border-b pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-blue-600 hover:text-blue-800">
                                                {{ $order->order_number }}
                                            </a>
                                            <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                            <span class="inline-block px-2 py-1 text-xs rounded mt-1
                                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $order->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No orders yet</p>
                    @endif
                </div>
            </div>

            <!-- Loyalty Points Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">
                        <i class="fas fa-coins text-yellow-500 mr-2"></i>Loyalty Points
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <p class="text-sm font-medium text-gray-600 mb-1">Current Balance</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ number_format($customer->loyalty_points_balance) }}</p>
                            <p class="text-xs text-gray-500 mt-1">points</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Earned</p>
                            <p class="text-3xl font-bold text-green-600">{{ number_format($customer->loyaltyPoints()->where('type', 'earned')->sum('points')) }}</p>
                            <p class="text-xs text-gray-500 mt-1">points</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Redeemed</p>
                            <p class="text-3xl font-bold text-blue-600">{{ number_format(abs($customer->loyaltyPoints()->where('type', 'redeemed')->sum('points'))) }}</p>
                            <p class="text-xs text-gray-500 mt-1">points</p>
                        </div>
                    </div>

                    <!-- Adjust Points Form -->
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Adjust Points</h4>
                        <form action="{{ route('admin.customers.loyalty-adjust', $customer) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <div class="flex-1">
                                <input type="number" name="points" required placeholder="Points (negative to deduct)"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="description" required placeholder="Reason for adjustment" maxlength="255"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-plus-minus mr-1"></i>Adjust
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
