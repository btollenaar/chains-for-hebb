@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tags</h1>
                <p class="text-gray-600 mt-1">Manage customer tags for segmentation and organization</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.tags.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>Create Tag
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 md:gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Tags</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_tags'] }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-tags text-blue-600 text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Assignments</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_assignments'] }}</p>
                        </div>
                        <div class="bg-purple-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-user-tag text-purple-600 text-lg md:text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section - Desktop Only -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.tags.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Tag name or description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.tags.index') }}" class="btn-admin-secondary">
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.tags.index') }}">
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Tag name or description"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Tags Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($tags->count() > 0)
                        <!-- Mobile Cards View -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($tags as $tag)
                                <div class="bg-white border rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <span class="inline-block w-4 h-4 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $tag->color }};"></span>
                                            <span class="font-semibold text-gray-900">{{ $tag->name }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:text-blue-800" aria-label="Edit tag">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" aria-label="Delete tag">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500 mb-2">
                                        <span class="font-medium">Slug:</span> {{ $tag->slug }}
                                    </div>
                                    <div class="text-sm text-gray-500 mb-2">
                                        <span class="font-medium">Customers:</span> {{ $tag->customers_count }}
                                    </div>
                                    @if($tag->description)
                                        <div class="text-sm text-gray-600 mt-2">{{ Str::limit($tag->description, 100) }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customers</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tags as $tag)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <span class="inline-block w-4 h-4 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $tag->color }};"></span>
                                                    <span class="text-sm font-medium text-gray-900">{{ $tag->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $tag->slug }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="font-semibold">{{ $tag->customers_count }}</span>
                                            </td>
                                            <td class="hidden lg:table-cell px-6 py-4 text-sm text-gray-500">
                                                {{ Str::limit($tag->description, 80) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-3">
                                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:text-blue-800" aria-label="Edit tag">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this tag? It will be removed from all customers.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-800" aria-label="Delete tag">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $tags->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No tags found</h3>
                            <p class="text-gray-500 mb-4">
                                @if(request()->hasAny(['search']))
                                    No tags match your current filters. Try adjusting your search criteria.
                                @else
                                    Create your first tag to start organizing customers.
                                @endif
                            </p>
                            <a href="{{ route('admin.tags.create') }}" class="btn-admin-primary">
                                <i class="fas fa-plus mr-2"></i>Create Tag
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
