@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Newsletter Subscribers</h1>
        <p class="text-gray-600 mt-1">Manage your email marketing list and export for campaigns</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards: Mobile-optimized (2 cols mobile, 2 cols tablet, 4 cols desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Subscribers</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-users text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Active</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['active']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $stats['active_percentage'] }}% of total</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Inactive</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-600 mt-2">{{ number_format($stats['inactive']) }}</p>
                        </div>
                        <div class="bg-gray-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-times-circle text-gray-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">This Month</p>
                            <p class="text-2xl md:text-3xl font-bold text-abs-primary mt-2">{{ number_format($stats['this_month']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">New subscribers</p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-chart-line text-abs-primary text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('admin.newsletter.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Email or name..."
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                    id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                            </select>
                        </div>

                        <!-- Source Filter -->
                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                            <select name="source"
                                    id="source"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All Sources</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source }}" {{ request('source') === $source ? 'selected' : '' }}>
                                        {{ ucfirst($source) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="flex-1 bg-abs-primary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.newsletter.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Export Button -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.newsletter.export', request()->query()) }}"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded transition-colors duration-200">
                        <i class="fas fa-file-csv mr-2"></i>Export to CSV
                        @if($subscriptions->total() > 0)
                            ({{ number_format($subscriptions->total()) }} {{ $subscriptions->total() === 1 ? 'subscriber' : 'subscribers' }})
                        @endif
                    </a>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Export respects current filters. Use for email marketing campaigns (Mailchimp, SendGrid, etc.)
                    </p>
                </div>
            </div>

            <!-- Subscribers List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($subscriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subscriber
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Source
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subscribed
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subscriptions as $subscription)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $subscription->email }}
                                                </div>
                                                @if($subscription->name)
                                                    <div class="text-sm text-gray-500">
                                                        {{ $subscription->name }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($subscription->source)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($subscription->source) }}
                                                </span>
                                            @else
                                                <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($subscription->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Active
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $subscription->subscribed_at ? $subscription->subscribed_at->format('M d, Y') : $subscription->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($subscription->is_active)
                                                <form action="{{ route('admin.newsletter.deactivate', $subscription) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Unsubscribe this email address?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            aria-label="Unsubscribe subscriber"
                                                            class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                        <i class="fas fa-ban" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.newsletter.activate', $subscription) }}"
                                                      method="POST"
                                                      class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            aria-label="Reactivate subscriber"
                                                            class="text-green-600 hover:text-green-900 mr-3">
                                                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.newsletter.destroy', $subscription) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Permanently delete this subscriber? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        aria-label="Delete subscriber"
                                                        class="link-admin-danger">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $subscriptions->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-envelope text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No subscribers found</h3>
                        <p class="text-gray-500">
                            @if(request()->hasAny(['search', 'status', 'source']))
                                No subscribers match your current filters. Try adjusting your search criteria.
                            @else
                                Subscribers will appear here when customers sign up for your newsletter.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
