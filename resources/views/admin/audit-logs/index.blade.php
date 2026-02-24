@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Audit Log</h1>
        <p class="text-gray-600 mt-1">Track all admin activity and model changes</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards: Mobile-optimized (2 cols mobile, 4 cols desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Entries</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-history text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Today</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['today']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-calendar-day text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">This Week</p>
                            <p class="text-2xl md:text-3xl font-bold text-purple-600 mt-2">{{ number_format($stats['this_week']) }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-calendar-week text-purple-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Unique Users</p>
                            <p class="text-2xl md:text-3xl font-bold text-teal-600 mt-2">{{ number_format($stats['unique_users']) }}</p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-users text-teal-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Filter Form -->
            <div class="hidden md:block bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- User Filter -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                            <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <select name="action" id="action" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                                <option value="">All Actions</option>
                                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="exported" {{ request('action') === 'exported' ? 'selected' : '' }}>Exported</option>
                                <option value="imported" {{ request('action') === 'imported' ? 'selected' : '' }}>Imported</option>
                            </select>
                        </div>

                        <!-- Model Type Filter -->
                        <div>
                            <label for="model_type" class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                            <select name="model_type" id="model_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                                <option value="">All Types</option>
                                @foreach($modelTypes as $type)
                                    <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search by label, IP, user name, or email..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                        </div>
                        <button type="submit" class="btn-admin-primary btn-admin-sm">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn-admin-secondary btn-admin-sm">
                            <i class="fas fa-redo mr-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal :formAction="route('admin.audit-logs.index')">
                <div class="space-y-4">
                    <!-- User Filter -->
                    <div>
                        <label for="mobile_user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                        <select name="user_id" id="mobile_user_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Filter -->
                    <div>
                        <label for="mobile_action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" id="mobile_action" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            <option value="exported" {{ request('action') === 'exported' ? 'selected' : '' }}>Exported</option>
                            <option value="imported" {{ request('action') === 'imported' ? 'selected' : '' }}>Imported</option>
                        </select>
                    </div>

                    <!-- Model Type Filter -->
                    <div>
                        <label for="mobile_model_type" class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                        <select name="model_type" id="mobile_model_type" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">All Types</option>
                            @foreach($modelTypes as $type)
                                <option value="{{ $type }}" {{ request('model_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="mobile_date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" id="mobile_date_from" value="{{ request('date_from') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="mobile_date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" id="mobile_date_to" value="{{ request('date_to') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    <!-- Search -->
                    <div>
                        <label for="mobile_search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="mobile_search" value="{{ request('search') }}"
                               placeholder="Search by label, IP, user..."
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Desktop Table -->
            <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Label</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">IP Address</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $log->created_at->format('M d, Y') }}<br>
                                        <span class="text-xs text-gray-400">{{ $log->created_at->format('g:i A') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($log->user)
                                            <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                        @else
                                            <span class="text-gray-400 italic">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($log->action)
                                            @case('created')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-plus-circle mr-1"></i> Created
                                                </span>
                                                @break
                                            @case('updated')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <i class="fas fa-edit mr-1"></i> Updated
                                                </span>
                                                @break
                                            @case('deleted')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-trash mr-1"></i> Deleted
                                                </span>
                                                @break
                                            @case('exported')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    <i class="fas fa-download mr-1"></i> Exported
                                                </span>
                                                @break
                                            @case('imported')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">
                                                    <i class="fas fa-upload mr-1"></i> Imported
                                                </span>
                                                @break
                                            @default
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ ucfirst($log->action) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="font-medium text-gray-900">{{ $log->model_type }}</span>
                                        @if($log->model_id)
                                            <span class="text-gray-400">#{{ $log->model_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                        {{ $log->model_label ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.audit-logs.show', $log) }}"
                                           class="text-admin-teal hover:underline font-medium">
                                            View <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-history text-4xl text-gray-300 mb-3 block"></i>
                                        <p class="text-lg font-medium">No audit log entries found</p>
                                        <p class="text-sm">Activity will appear here as changes are made.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($logs as $log)
                    <a href="{{ route('admin.audit-logs.show', $log) }}" class="bg-white rounded-lg shadow-md p-4 block hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                @switch($log->action)
                                    @case('created')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-plus-circle mr-1"></i> Created
                                        </span>
                                        @break
                                    @case('updated')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-edit mr-1"></i> Updated
                                        </span>
                                        @break
                                    @case('deleted')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-trash mr-1"></i> Deleted
                                        </span>
                                        @break
                                    @case('exported')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            <i class="fas fa-download mr-1"></i> Exported
                                        </span>
                                        @break
                                    @case('imported')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">
                                            <i class="fas fa-upload mr-1"></i> Imported
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                @endswitch
                            </div>
                            <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $log->model_type }}@if($log->model_id)<span class="text-gray-400"> #{{ $log->model_id }}</span>@endif
                            </p>
                            @if($log->model_label)
                                <p class="text-sm text-gray-600 truncate">{{ $log->model_label }}</p>
                            @endif
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-user mr-1"></i>
                                {{ $log->user->name ?? 'System' }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500">
                        <i class="fas fa-history text-4xl text-gray-300 mb-3 block"></i>
                        <p class="text-lg font-medium">No audit log entries found</p>
                        <p class="text-sm">Activity will appear here as changes are made.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
