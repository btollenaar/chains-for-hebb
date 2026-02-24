@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900 leading-tight">
                Product Categories
            </h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.products.index') }}"
                   class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Products
                </a>
                <a href="{{ route('admin.products.categories.create') }}"
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="h-12 w-12 rounded object-cover">
                                        @else
                                        <div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($category->depth > 0)
                                            <span class="text-gray-400 mr-2">
                                                {{ str_repeat('└─ ', $category->depth) }}
                                            </span>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                                @if($category->depth > 0)
                                                <div class="text-xs text-gray-500" title="Full path">
                                                    {{ $category->getFullPath() }}
                                                </div>
                                                @elseif($category->description)
                                                <div class="text-sm text-gray-500">{{ Str::limit($category->description, 60) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->slug }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->products_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->display_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($category->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.products.categories.edit', $category) }}"
                                           class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.products.categories.destroy', $category) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone if products are assigned.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="link-admin-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $categories->appends(request()->query())->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No product categories yet.</p>
                        <a href="{{ route('admin.products.categories.create') }}"
                           class="inline-block bg-abs-primary hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                            Create Your First Category
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
