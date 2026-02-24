@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $coupon->code }}</h1>
                <p class="text-gray-600 mt-1">{{ $coupon->description ?? 'No description' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.coupons.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-admin-primary">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Coupon Details -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Coupon Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Code</p>
                                <p class="font-mono font-bold text-gray-900 text-lg">{{ $coupon->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Discount</p>
                                <p class="font-bold text-admin-teal text-lg">{{ $coupon->formatted_value }} off</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Type</p>
                                <p class="font-semibold text-gray-900">{{ ucfirst($coupon->type) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                @if(!$coupon->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @elseif($coupon->is_expired)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                @elseif($coupon->is_maxed_out)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Maxed Out</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @endif
                            </div>
                            @if($coupon->min_order_amount)
                            <div>
                                <p class="text-sm text-gray-500">Min Order Amount</p>
                                <p class="font-semibold text-gray-900">${{ number_format($coupon->min_order_amount, 2) }}</p>
                            </div>
                            @endif
                            @if($coupon->max_discount_amount)
                            <div>
                                <p class="text-sm text-gray-500">Max Discount</p>
                                <p class="font-semibold text-gray-900">${{ number_format($coupon->max_discount_amount, 2) }}</p>
                            </div>
                            @endif
                            @if($coupon->starts_at)
                            <div>
                                <p class="text-sm text-gray-500">Starts</p>
                                <p class="font-semibold text-gray-900">{{ $coupon->starts_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                            @if($coupon->expires_at)
                            <div>
                                <p class="text-sm text-gray-500">Expires</p>
                                <p class="font-semibold text-gray-900">{{ $coupon->expires_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Usage History -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Usage History</h3>

                        @if($usageHistory->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($usageHistory as $usage)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">
                                            @if($usage->customer)
                                                <a href="{{ route('admin.customers.show', $usage->customer) }}" class="text-admin-teal hover:underline">
                                                    {{ $usage->customer->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">Deleted</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($usage->order)
                                                <a href="{{ route('admin.orders.show', $usage->order) }}" class="text-admin-teal hover:underline">
                                                    {{ $usage->order->order_number }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">Deleted</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">
                                            -${{ number_format($usage->discount_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-500">
                                            {{ $usage->used_at->format('M j, Y g:i A') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($usageHistory->hasPages())
                            <div class="mt-4">
                                {{ $usageHistory->links() }}
                            </div>
                        @endif
                        @else
                        <p class="text-gray-500 text-center py-6">This coupon has not been used yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Statistics</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Times Used</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}
                                </p>
                            </div>
                            @if($coupon->max_uses_per_customer)
                            <div>
                                <p class="text-sm text-gray-500">Per Customer Limit</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $coupon->max_uses_per_customer }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500">Total Savings Generated</p>
                                <p class="text-2xl font-bold text-green-600">${{ number_format($totalSavings, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $coupon->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <form method="POST" action="{{ route('admin.coupons.toggle-active', $coupon) }}">
                                @csrf
                                <button type="submit" class="w-full {{ $coupon->is_active ? 'btn-admin-secondary' : 'btn-admin-success' }}">
                                    <i class="fas {{ $coupon->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-2"></i>
                                    {{ $coupon->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Delete Coupon
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
