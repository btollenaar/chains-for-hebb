@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Donations</h1>
        <p class="text-gray-600 mt-1">View and manage all donations and recurring contributions</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Raised</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">${{ number_format($stats['total_raised'], 2) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-dollar-sign text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Donations</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_count']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-heart text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Recurring MRR</p>
                            <p class="text-2xl md:text-3xl font-bold text-admin-teal mt-2">${{ number_format($stats['recurring_mrr'], 2) }}</p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-sync-alt text-admin-teal text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="{{ route('admin.donations.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Donor name or email..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                        </div>
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select name="payment_status" id="payment_status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                                <option value="">All Statuses</option>
                                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>
                        <div>
                            <label for="donation_type" class="block text-sm font-medium text-gray-700 mb-1">Donation Type</label>
                            <select name="donation_type" id="donation_type"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal sm:text-sm">
                                <option value="">All Types</option>
                                <option value="one_time" {{ request('donation_type') === 'one_time' ? 'selected' : '' }}>One-Time</option>
                                <option value="recurring" {{ request('donation_type') === 'recurring' ? 'selected' : '' }}>Recurring</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-admin-primary btn-admin-sm">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.donations.index') }}" class="btn-admin-secondary btn-admin-sm">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Donations Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Tier</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($donations as $donation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $donation->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $donation->donor_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $donation->donor_email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                        ${{ number_format($donation->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($donation->donation_type === 'recurring')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Recurring
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                One-Time
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        {{ $donation->tier->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $statusColors = [
                                                'paid' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$donation->payment_status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                            {{ ucfirst($donation->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.donations.show', $donation) }}" class="text-admin-teal hover:text-teal-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-heart text-gray-300 text-4xl mb-3 block"></i>
                                        No donations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($donations->hasPages())
                <div class="mt-6">
                    {{ $donations->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
