@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">
            Create Blog Post
        </h1>
    </div>

    <div class="pb-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Title *
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title') }}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category *
                                </label>
                                <select name="category_id"
                                        id="category_id"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                                Excerpt
                            </label>
                            <textarea name="excerpt"
                                      id="excerpt"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary tinymce">{{ old('excerpt') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Short summary of the post</p>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content *
                            </label>
                            <textarea name="content"
                                      id="content"
                                      rows="15"
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary tinymce">{{ old('content') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Use the rich text editor for formatting</p>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image
                            </label>
                            <input type="file"
                                   name="featured_image"
                                   id="featured_image"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="featured_image_alt" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image Alt Text
                            </label>
                            <input type="text"
                                   name="featured_image_alt"
                                   id="featured_image_alt"
                                   value="{{ old('featured_image_alt') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary"
                                   placeholder="Describe the image for accessibility">
                            <p class="mt-1 text-sm text-gray-500">Descriptive text for screen readers and SEO</p>
                            @error('featured_image_alt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="published"
                                       value="1"
                                       {{ old('published') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary">
                                <span class="ml-2 text-sm text-gray-700">Publish immediately</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.blog.posts.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Posts
                            </a>
                            <button type="submit"
                                    class="btn-admin-primary">
                                Create Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/kh3vhfgxdfo6kz7tzjfulah6hs735glyg7cr378gob5ljlg3/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        height: 300,
        menubar: false,
        plugins: 'lists link code help',
        toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link | code | help',
        valid_elements: 'p,br,strong/b,em/i,u,a[href|title|target],ul,ol,li,blockquote,code,pre',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }'
    });
</script>
@endpush
