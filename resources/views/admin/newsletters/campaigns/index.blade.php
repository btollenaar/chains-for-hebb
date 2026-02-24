@extends('layouts.admin')

@section('title', 'Newsletter Campaigns')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900">Newsletter Campaigns</h1>
                <p class="text-gray-600 mt-1">Create and manage email campaigns</p>
            </div>
            <a href="{{ route('admin.newsletters.campaigns.create') }}" class="btn-admin-primary">
                <i class="fas fa-plus mr-2"></i> Create Campaign
            </a>
        </div>

        <!-- Stats Cards: Mobile-optimized (2 cols mobile, 3 cols tablet, 5 cols desktop) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="text-xs md:text-sm text-gray-500 mb-1">Total Campaigns</div>
                <div class="text-2xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="text-xs md:text-sm text-gray-500 mb-1">Drafts</div>
                <div class="text-2xl md:text-3xl font-bold text-yellow-600">{{ number_format($stats['drafts']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="text-xs md:text-sm text-gray-500 mb-1">Scheduled</div>
                <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ number_format($stats['scheduled']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="text-xs md:text-sm text-gray-500 mb-1">Sent</div>
                <div class="text-2xl md:text-3xl font-bold text-green-600">{{ number_format($stats['sent']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="text-xs md:text-sm text-gray-500 mb-1">Avg Open Rate</div>
                <div class="text-2xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['avg_open_rate'], 1) }}%</div>
            </div>
        </div>

        <!-- Filters Section - Desktop Only -->
        <div class="hidden md:block bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.newsletters.campaigns.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by subject..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>Sending</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-admin-primary mr-2">
                            <i class="fas fa-filter mr-2"></i> Filter
                        </button>
                        <a href="{{ route('admin.newsletters.campaigns.index') }}" class="btn-admin-secondary">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mobile Filter Modal -->
        <x-admin.mobile-filter-modal formAction="{{ route('admin.newsletters.campaigns.index') }}">
            <!-- Search -->
            <div>
                <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                       placeholder="Search by subject..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="mobile-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="mobile-status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>Sending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </x-admin.mobile-filter-modal>

        <!-- Campaigns Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($campaigns->count() > 0)
                <!-- Mobile Cards View -->
                <div class="grid grid-cols-1 gap-4 md:hidden p-4">
                    @foreach($campaigns as $campaign)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $campaign->subject }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Lists: {{ $campaign->lists->pluck('name')->join(', ') }}
                                    </div>
                                </div>
                                <div class="ml-2 flex gap-2">
                                    <a href="{{ route('admin.newsletters.campaigns.show', $campaign) }}" class="text-admin-teal">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($campaign->status === 'draft')
                                        <a href="{{ route('admin.newsletters.campaigns.edit', $campaign) }}" class="text-indigo-600">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Status:</span>
                                    @if($campaign->status === 'draft')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                    @elseif($campaign->status === 'scheduled')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                                    @elseif($campaign->status === 'sending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Sending</span>
                                    @elseif($campaign->status === 'sent')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Sent</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($campaign->status) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Recipients:</span>
                                    <span>{{ number_format($campaign->recipient_count) }}</span>
                                </div>
                                @if($campaign->status === 'sent')
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500">Performance:</span>
                                        <span>{{ number_format($campaign->open_rate, 1) }}% opens / {{ number_format($campaign->click_rate, 1) }}% clicks</span>
                                    </div>
                                @endif
                                @if($campaign->status === 'scheduled' && $campaign->scheduled_at)
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-500">Scheduled:</span>
                                        <span>{{ $campaign->scheduled_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Created:</span>
                                    <span>{{ $campaign->created_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                                <form action="{{ route('admin.newsletters.campaigns.duplicate', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-admin-teal hover:underline">
                                        <i class="fas fa-copy mr-1"></i> Duplicate
                                    </button>
                                </form>
                                @if(in_array($campaign->status, ['draft', 'cancelled']))
                                    <form action="{{ route('admin.newsletters.campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this campaign?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($campaigns as $campaign)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->subject }}</div>
                                    @if($campaign->preview_text)
                                        <div class="text-sm text-gray-500">{{ Str::limit($campaign->preview_text, 50) }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1">
                                        Lists: {{ $campaign->lists->pluck('name')->join(', ') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($campaign->status === 'draft')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                    @elseif($campaign->status === 'scheduled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Scheduled
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">{{ $campaign->scheduled_at->format('M j, Y g:i A') }}</div>
                                    @elseif($campaign->status === 'sending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Sending</span>
                                    @elseif($campaign->status === 'sent')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sent</span>
                                        <div class="text-xs text-gray-500 mt-1">{{ $campaign->sent_at->format('M j, Y') }}</div>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($campaign->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($campaign->recipient_count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($campaign->status === 'sent')
                                        <div class="text-sm text-gray-900">
                                            <span class="font-medium">{{ number_format($campaign->open_rate, 1) }}%</span> opens
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium">{{ number_format($campaign->click_rate, 1) }}%</span> clicks
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $campaign->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.newsletters.campaigns.show', $campaign) }}" class="text-admin-teal hover:text-admin-teal/80 mr-3">
                                        View
                                    </a>
                                    @if($campaign->status === 'draft')
                                        <a href="{{ route('admin.newsletters.campaigns.edit', $campaign) }}" class="text-admin-teal hover:text-admin-teal/80 mr-3">
                                            Edit
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.newsletters.campaigns.duplicate', $campaign) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-admin-teal hover:text-admin-teal/80 mr-3">
                                            Duplicate
                                        </button>
                                    </form>
                                    @if(in_array($campaign->status, ['draft', 'cancelled']))
                                        <form action="{{ route('admin.newsletters.campaigns.destroy', $campaign) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this campaign?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $campaigns->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-envelope text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No campaigns found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first newsletter campaign.</p>
                    <a href="{{ route('admin.newsletters.campaigns.create') }}" class="btn-admin-primary">
                        <i class="fas fa-plus mr-2"></i> Create Campaign
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
