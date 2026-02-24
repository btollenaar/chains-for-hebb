@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">
            Create Product Category
        </h1>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.products.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Name *
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                Slug (leave blank to auto-generate)
                            </label>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            <p class="mt-1 text-sm text-gray-500">URL-friendly version of the name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Parent Category (optional)
                            </label>
                            <select name="parent_id"
                                    id="parent_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                <option value="">— Top Level —</option>
                                @foreach($categoryOptions as $option)
                                    <option value="{{ $option['id'] }}" {{ old('parent_id') == $option['id'] ? 'selected' : '' }}>
                                        {{ $option['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Select a parent to create a subcategory</p>
                            @error('parent_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Image
                            </label>
                            <input type="file"
                                   name="image"
                                   id="image"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order
                            </label>
                            <input type="number"
                                   name="display_order"
                                   id="display_order"
                                   value="{{ old('display_order', 0) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            @error('display_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">Inactive categories won't appear on the frontend</p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.products.categories.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
                            </a>
                            <button type="submit"
                                    class="btn-admin-primary">
                                Create Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
