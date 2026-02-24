@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">
            Edit Blog Category: {{ $category->name }}
        </h1>
    </div>

    <div class="pb-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.blog.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Name *
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $category->name) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                Slug
                            </label>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug', $category->slug) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            <p class="mt-1 text-sm text-gray-500">URL-friendly version of the name</p>
                            @error('slug')
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.blog.categories.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
                            </a>
                            <button type="submit"
                                    class="btn-admin-primary">
                                Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
