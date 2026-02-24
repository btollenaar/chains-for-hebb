@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Coupons</h1>
                <p class="text-gray-600 mt-1">Manage discount codes and promotions</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.coupons.export') }}" class="btn-admin-secondary btn-admin-sm">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
                <a href="{{ route('admin.coupons.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>Create Coupon
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Coupons</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats->total) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-ticket-alt text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.coupons.index', ['status' => 'active']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Active</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats->active) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.coupons.index', ['status' => 'expired']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Expired</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-2">{{ number_format($stats->expired) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-clock text-red-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Savings</p>
                            <p class="text-2xl md:text-3xl font-bold text-abs-primary mt-2">${{ number_format($totalSavings, 2) }}</p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-dollar-sign text-abs-primary text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters - Desktop Only -->
            <div class="hidden md:block bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('admin.coupons.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Code or description..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                                <option value="">All Types</option>
                                <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-admin-primary btn-admin-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn-admin-secondary btn-admin-sm">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.coupons.index') }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Code or description..."
                               class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Mobile Cards -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($coupons as $coupon)
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="font-mono font-bold text-lg text-gray-900">{{ $coupon->code }}</span>
                                @if($coupon->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $coupon->description }}</p>
                                @endif
                            </div>
                            @if(!$coupon->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @elseif($coupon->is_expired)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                            @elseif($coupon->is_maxed_out)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Maxed Out</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-admin-teal">{{ $coupon->formatted_value }} off</span>
                            <span class="text-sm text-gray-500">Used {{ $coupon->used_count }}{{ $coupon->max_uses ? '/' . $coupon->max_uses : '' }} times</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn-admin-secondary btn-admin-sm flex-1 text-center">View</a>
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-admin-primary btn-admin-sm flex-1 text-center">Edit</a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500">
                        No coupons found.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Dates</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($coupons as $coupon)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-gray-900">{{ $coupon->code }}</div>
                                        @if($coupon->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($coupon->description, 40) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-semibold text-admin-teal">{{ $coupon->formatted_value }}</span>
                                        @if($coupon->min_order_amount)
                                            <div class="text-xs text-gray-500">Min: ${{ number_format($coupon->min_order_amount, 2) }}</div>
                                        @endif
                                        @if($coupon->type === 'percentage' && $coupon->max_discount_amount)
                                            <div class="text-xs text-gray-500">Cap: ${{ number_format($coupon->max_discount_amount, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $coupon->used_count }}{{ $coupon->max_uses ? ' / ' . $coupon->max_uses : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        @if($coupon->starts_at)
                                            <div>From: {{ $coupon->starts_at->format('M j, Y') }}</div>
                                        @endif
                                        @if($coupon->expires_at)
                                            <div>Until: {{ $coupon->expires_at->format('M j, Y') }}</div>
                                        @endif
                                        @if(!$coupon->starts_at && !$coupon->expires_at)
                                            <span class="text-gray-400">No limits</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if(!$coupon->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @elseif($coupon->is_expired)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                        @elseif($coupon->is_maxed_out)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Maxed Out</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-gray-600 hover:text-gray-900" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-admin-teal hover:text-teal-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.coupons.toggle-active', $coupon) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="{{ $coupon->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}" title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas {{ $coupon->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No coupons found. <a href="{{ route('admin.coupons.create') }}" class="text-admin-teal hover:underline">Create one</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($coupons->hasPages())
                <div class="mt-6">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
