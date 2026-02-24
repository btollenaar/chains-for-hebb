@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                    Blog Posts
                </h1>
                <p class="text-gray-600 mt-1">Create and manage blog content</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.blog.categories.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-folder mr-2"></i>Manage Categories
                </a>
                <a href="{{ route('admin.blog.posts.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>New Post
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters Section - Desktop Only -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.blog.posts.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Title or content"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category_id" id="category_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Categories</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">All Statuses</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                        </div>

                        <!-- Filter Buttons (Full Width on Next Row) -->
                        <div class="md:col-span-5 flex gap-2">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.blog.posts.index') }}" class="btn-admin-secondary">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.blog.posts.index') }}">
                <!-- Search -->
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Title or content"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="mobile-category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" id="mobile-category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Categories</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="mobile-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="mobile-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Statuses</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="mobile-date-from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" id="mobile-date-from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Date To -->
                <div>
                    <label for="mobile-date-to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" id="mobile-date-to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Bulk Actions Bar (Hidden by default, shown when items selected) -->
            <div id="bulk-actions-bar" class="hidden bg-abs-primary text-white p-4 rounded-lg shadow-lg mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="font-semibold"><span id="selected-count">0</span> item(s) selected</span>
                        <form id="bulk-actions-form" action="{{ route('admin.blog.posts.bulk') }}" method="POST" class="flex items-center space-x-3">
                            @csrf
                            <select id="bulk-action" class="px-4 py-2 rounded border border-gray-300 text-gray-900 focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">Choose Action</option>
                                <option value="publish">Publish Selected</option>
                                <option value="unpublish">Unpublish Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="button" onclick="applyBulkAction(event)" class="btn-admin-secondary">
                                Apply
                            </button>
                            <input type="hidden" id="bulk-action-input" name="action" value="">
                            <div id="bulk-ids-container"></div>
                        </form>
                    </div>
                    <button onclick="document.querySelectorAll('.select-item').forEach(cb => cb.checked = false); document.getElementById('select-all').checked = false; this.closest('#bulk-actions-bar').classList.add('hidden');" class="text-white hover:text-gray-200">
                        <i class="fas fa-times"></i> Clear Selection
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($posts->count() > 0)
                        <!-- Mobile Cards View - Visible only on mobile -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($posts as $post)
                                <div class="bg-white rounded-lg shadow-md p-4 relative">
                                    <!-- Checkbox in top-right corner -->
                                    <div class="absolute top-4 right-4">
                                        <input type="checkbox" class="select-item rounded border-gray-300 text-abs-primary focus:ring-abs-primary" value="{{ $post->id }}" aria-label="Select blog post {{ $post->title }}">
                                    </div>

                                    <!-- Post Content -->
                                    <div class="space-y-3 pr-8">
                                        <!-- Title with Image -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Post</p>
                                            <div class="mt-1">
                                                <div class="flex items-start">
                                                    @if($post->featured_image)
                                                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                                                             alt="{{ $post->title }}"
                                                             class="w-16 h-16 object-cover rounded mr-3 flex-shrink-0">
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-gray-900">{{ $post->title }}</div>
                                                        <div class="text-sm text-gray-500 truncate">{{ $post->slug }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Category -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Category</p>
                                            <div class="mt-1">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $post->category->name }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Status</p>
                                            <div class="mt-1">
                                                @if($post->published)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Published
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Draft
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Date -->
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Date</p>
                                            <div class="mt-1 text-sm text-gray-900">
                                                @if($post->published_at)
                                                    {{ $post->published_at->format('M d, Y') }}
                                                @else
                                                    {{ $post->created_at->format('M d, Y') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-4 pt-4 border-t flex justify-end gap-3">
                                        @if($post->published)
                                            <a href="{{ route('blog.show', $post->slug) }}"
                                               target="_blank"
                                               aria-label="View published post"
                                               class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.blog.posts.edit', $post) }}"
                                           aria-label="Edit post"
                                           class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.blog.posts.destroy', $post) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    aria-label="Delete post"
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Desktop Table - Hidden on mobile -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary" aria-label="Select all blog posts">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($posts as $post)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <input type="checkbox" class="select-item rounded border-gray-300 text-abs-primary focus:ring-abs-primary" value="{{ $post->id }}" aria-label="Select blog post {{ $post->title }}">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @if($post->featured_image)
                                                <img src="{{ asset('storage/' . $post->featured_image) }}"
                                                     alt="{{ $post->title }}"
                                                     class="w-16 h-16 object-cover rounded mr-4">
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ $post->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $post->category->name }}
                                            </span>
                                        </td>
                                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $post->author->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($post->published)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Published
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($post->published_at)
                                                {{ $post->published_at->format('M d, Y') }}
                                            @else
                                                {{ $post->created_at->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($post->published)
                                            <a href="{{ route('blog.show', $post->slug) }}"
                                               target="_blank"
                                               aria-label="View published post in new tab"
                                               class="text-green-600 hover:text-green-900 mr-3">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('admin.blog.posts.edit', $post) }}"
                                               aria-label="Edit post"
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-edit" aria-hidden="true"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.blog.posts.destroy', $post) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        aria-label="Delete post"
                                                        class="link-admin-danger">
                                                    <i class="fas fa-trash" aria-hidden="true"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-newspaper text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No blog posts yet.</p>
                        <a href="{{ route('admin.blog.posts.create') }}"
                           class="inline-block bg-abs-primary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                            Create Your First Post
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
