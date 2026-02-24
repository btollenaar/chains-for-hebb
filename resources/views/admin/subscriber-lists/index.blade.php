@extends('layouts.admin')

@section('title', 'Subscriber Lists')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Subscriber Lists</h1>
            <p class="text-gray-600 mt-1">Manage email newsletter subscriber lists</p>
        </div>
        <a href="{{ route('admin.subscriber-lists.create') }}" class="btn-admin-primary">
            <i class="fas fa-plus mr-2"></i>
            Create New List
        </a>
    </div>

    <!-- Filters Section - Desktop Only -->
    <div class="hidden md:block bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.subscriber-lists.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ request('search') }}"
                       placeholder="Name or description..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
            </div>

            <!-- Type Filter -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type"
                        id="type"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                    <option value="">All Lists</option>
                    <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System Lists</option>
                    <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom Lists</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-admin-primary flex-1">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.subscriber-lists.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Mobile Filter Modal -->
    <x-admin.mobile-filter-modal formAction="{{ route('admin.subscriber-lists.index') }}">
        <!-- Search -->
        <div>
            <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                   placeholder="Name or description..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
        </div>

        <!-- Type Filter -->
        <div>
            <label for="mobile-type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select name="type" id="mobile-type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                <option value="">All Lists</option>
                <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System Lists</option>
                <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom Lists</option>
            </select>
        </div>
    </x-admin.mobile-filter-modal>

    <!-- Lists Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($lists->count() > 0)
            <!-- Mobile Cards View -->
            <div class="grid grid-cols-1 gap-4 md:hidden p-4">
                @foreach($lists as $list)
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900">{{ $list->name }}</div>
                                @if($list->description)
                                    <div class="text-sm text-gray-500 mt-1">{{ Str::limit($list->description, 60) }}</div>
                                @endif
                            </div>
                            <div class="ml-2 flex gap-2">
                                <a href="{{ route('admin.subscriber-lists.show', $list) }}" class="text-admin-teal">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!$list->is_system)
                                    <a href="{{ route('admin.subscriber-lists.edit', $list) }}" class="text-indigo-600">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Type:</span>
                                @if($list->is_system)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-cog mr-1"></i>System
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-user-edit mr-1"></i>Custom
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Subscribers:</span>
                                <a href="{{ route('admin.subscriber-lists.show', $list) }}" class="text-admin-teal hover:underline">
                                    {{ number_format($list->subscribers_count) }}
                                </a>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Created:</span>
                                <span>{{ $list->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @if(!$list->is_system)
                            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                                <form action="{{ route('admin.subscriber-lists.destroy', $list) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this list?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                                <i class="fas fa-lock mr-1"></i> System lists cannot be modified
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Slug
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscribers
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($lists as $list)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $list->name }}
                                            </div>
                                            @if($list->description)
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit($list->description, 50) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 font-mono">
                                        {{ $list->slug }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($list->is_system)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-cog mr-1"></i>
                                            System
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-user-edit mr-1"></i>
                                            Custom
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('admin.subscriber-lists.show', $list) }}"
                                       class="text-admin-teal hover:underline">
                                        {{ number_format($list->subscribers_count) }}
                                        <i class="fas fa-external-link-alt text-xs ml-1"></i>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $list->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.subscriber-lists.show', $list) }}"
                                           class="text-gray-600 hover:text-admin-teal"
                                           title="View Subscribers">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if(!$list->is_system)
                                            <a href="{{ route('admin.subscriber-lists.edit', $list) }}"
                                               class="text-gray-600 hover:text-admin-teal"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.subscriber-lists.destroy', $list) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this list? All subscriber associations will be removed.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400" title="System lists cannot be modified">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $lists->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-list-ul text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">
                    @if(request()->hasAny(['search', 'type']))
                        No subscriber lists found matching your filters.
                    @else
                        No subscriber lists created yet.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'type']))
                    <a href="{{ route('admin.subscriber-lists.create') }}" class="btn-admin-primary mt-4">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First List
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
