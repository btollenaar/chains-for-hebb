@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Return Requests</h1>
                <p class="text-gray-600 mt-1">Manage customer return and refund requests</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-gray-600">Total</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats->total) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-yellow-600">Pending</p>
            <p class="text-2xl md:text-3xl font-bold text-yellow-700 mt-2">{{ number_format($stats->requested) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-green-600">Approved</p>
            <p class="text-2xl md:text-3xl font-bold text-green-700 mt-2">{{ number_format($stats->approved) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-blue-600">Completed</p>
            <p class="text-2xl md:text-3xl font-bold text-blue-700 mt-2">{{ number_format($stats->completed) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-red-600">Rejected</p>
            <p class="text-2xl md:text-3xl font-bold text-red-700 mt-2">{{ number_format($stats->rejected) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <p class="text-xs md:text-sm font-medium text-gray-600">Total Refunded</p>
            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">${{ number_format($stats->total_refunded ?? 0, 2) }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="hidden md:block bg-white shadow-sm rounded-lg mb-6">
        <form method="GET" action="{{ route('admin.returns.index') }}" class="p-4 flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm"
                       placeholder="Return #, order #, customer...">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                    <option value="">All Statuses</option>
                    <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-admin-primary btn-admin-sm">Filter</button>
                <a href="{{ route('admin.returns.index') }}" class="btn-admin-secondary btn-admin-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- Mobile Filter --}}
    <x-admin.mobile-filter-modal formAction="{{ route('admin.returns.index') }}">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Return #, order #, customer...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">All</option>
                    <option value="requested" {{ request('status') == 'requested' ? 'selected' : '' }}>Requested</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
        </div>
    </x-admin.mobile-filter-modal>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Refund</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($returns as $return)
                    @php
                        $statusBadge = [
                            'requested' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'completed' => 'bg-blue-100 text-blue-800',
                        ][$return->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $return->return_number }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.orders.show', $return->order) }}" class="text-admin-teal hover:underline">
                                {{ $return->order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $return->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ \App\Models\ReturnRequest::reasonOptions()[$return->reason] ?? $return->reason }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                                {{ ucfirst($return->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $return->refund_amount ? '$' . number_format($return->refund_amount, 2) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $return->created_at->format('M j, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.returns.show', $return) }}" class="btn-admin-secondary btn-admin-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-undo text-3xl mb-3 text-gray-300"></i>
                            <p>No return requests found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="grid grid-cols-1 gap-4 md:hidden">
        @forelse($returns as $return)
            @php
                $statusBadge = [
                    'requested' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                ][$return->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <a href="{{ route('admin.returns.show', $return) }}" class="block bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">{{ $return->return_number }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                        {{ ucfirst($return->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $return->customer->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">Order: {{ $return->order->order_number }}</p>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                    <span class="text-xs text-gray-400">{{ $return->created_at->format('M j, Y') }}</span>
                    @if($return->refund_amount)
                        <span class="text-sm font-semibold text-gray-900">${{ number_format($return->refund_amount, 2) }}</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                <i class="fas fa-undo text-3xl mb-3 text-gray-300"></i>
                <p>No return requests found.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $returns->links() }}
    </div>
@endsection
