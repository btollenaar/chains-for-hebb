@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                Blog Categories
            </h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.blog.posts.index') }}"
                   class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Posts
                </a>
                <a href="{{ route('admin.blog.categories.create') }}"
                   class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>New Category
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($categories->count() > 0)
                        <!-- Mobile Cards View - Visible only on mobile -->
                        <div class="grid grid-cols-1 gap-4 md:hidden mb-6">
                            @foreach($categories as $category)
                                <x-admin.table-card
                                    :item="$category"
                                    route="admin.blog.categories.edit"
                                    :fields="[
                                        [
                                            'label' => 'Category',
                                            'render' => function($item) {
                                                $html = '<div class=\'font-medium text-gray-900\'>' . e($item->name) . '</div>';
                                                if ($item->description) {
                                                    $html .= '<div class=\'text-sm text-gray-500 mt-1\'>' . e(Str::limit($item->description, 60)) . '</div>';
                                                }
                                                return $html;
                                            }
                                        ],
                                        [
                                            'label' => 'Posts',
                                            'render' => function($item) {
                                                return '<div class=\'text-sm text-gray-900\'><span class=\'font-semibold\'>' . $item->posts_count . '</span> posts</div>';
                                            }
                                        ],
                                        [
                                            'label' => 'Created',
                                            'render' => function($item) {
                                                return '<div class=\'text-sm text-gray-900\'>' . $item->created_at->format('M d, Y') . '</div>';
                                            }
                                        ]
                                    ]"
                                    :actions="[
                                        ['route' => 'admin.blog.categories.edit', 'icon' => 'fa-edit', 'color' => 'blue', 'label' => 'Edit category']
                                    ]"
                                />
                            @endforeach
                        </div>

                        <!-- Desktop Table - Hidden on mobile -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($categories as $category)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            @if($category->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($category->description, 60) }}</div>
                                            @endif
                                        </td>
                                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $category->slug }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $category->posts_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $category->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.blog.categories.edit', $category) }}"
                                               aria-label="Edit category {{ $category->name }}"
                                               class="link-admin-info mr-3">
                                                <i class="fas fa-edit" aria-hidden="true"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.blog.categories.destroy', $category) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this category? All posts in this category will also be deleted.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        aria-label="Delete category {{ $category->name }}"
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
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No blog categories yet.</p>
                        <a href="{{ route('admin.blog.categories.create') }}"
                           class="btn-admin-primary">
                            Create Your First Category
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
