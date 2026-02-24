@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 leading-tight">
            Edit Blog Post: {{ $post->title }}
        </h1>
    </div>

    <div class="pb-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.blog.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Title *
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       value="{{ old('title', $post->title) }}"
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
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
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
                                Slug
                            </label>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   value="{{ old('slug', $post->slug) }}"
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary tinymce">{{ old('excerpt', $post->excerpt) }}</textarea>
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary tinymce">{{ old('content', $post->content) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Use the rich text editor for formatting</p>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image
                            </label>
                            @if($post->featured_image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $post->featured_image) }}"
                                     alt="{{ $post->featured_image_alt ?: $post->title }}"
                                     class="w-48 h-32 object-cover rounded">
                                <p class="text-sm text-gray-500 mt-1">Current image</p>
                            </div>
                            @endif
                            <input type="file"
                                   name="featured_image"
                                   id="featured_image"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-abs-primary focus:border-abs-primary">
                            <p class="mt-1 text-sm text-gray-500">Upload a new image to replace the current one</p>
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
                                   value="{{ old('featured_image_alt', $post->featured_image_alt) }}"
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
                                       {{ old('published', $post->published) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary">
                                <span class="ml-2 text-sm text-gray-700">Published</span>
                            </label>
                            @if($post->published_at)
                            <p class="text-sm text-gray-500 mt-1">Published on {{ $post->published_at->format('F d, Y \a\t g:i A') }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.blog.posts.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Posts
                            </a>
                            <button type="submit"
                                    class="btn-admin-primary">
                                Update Post
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
