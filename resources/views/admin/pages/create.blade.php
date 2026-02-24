@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Page</h1>
        <p class="text-gray-600 mt-1">Add a new CMS page to your site</p>
    </div>

    <div class="pb-12">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title & Slug -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" required
                                   value="{{ old('title') }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('title'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('title') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slug" id="slug" required
                                   value="{{ old('slug') }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('slug'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('slug') }}</p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500">URL-friendly identifier (auto-generated from title).</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mb-6">
                        <label for="content-editor" class="block text-sm font-medium text-gray-700 mb-1">
                            Content <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content-editor" rows="15"
                                  class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('content') }}</textarea>
                        @if($errors->first('content'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('content') }}</p>
                        @endif
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-6">
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Excerpt</label>
                        <textarea name="excerpt" id="excerpt" rows="3"
                                  class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('excerpt') }}</textarea>
                        @if($errors->first('excerpt'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('excerpt') }}</p>
                        @endif
                        <p class="mt-1 text-xs text-gray-500">A short summary of the page content.</p>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-6">
                        <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                        <input type="file" name="featured_image" id="featured_image" accept="image/*"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @if($errors->first('featured_image'))
                            <p class="mt-1 text-sm text-red-600">{{ $errors->first('featured_image') }}</p>
                        @endif
                    </div>

                    <!-- Template & Parent -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                            <select name="template" id="template"
                                    class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                                <option value="default" {{ old('template', 'default') === 'default' ? 'selected' : '' }}>Default</option>
                                <option value="faq" {{ old('template') === 'faq' ? 'selected' : '' }}>FAQ</option>
                                <option value="course-plan" {{ old('template') === 'course-plan' ? 'selected' : '' }}>Course Plan</option>
                                <option value="how-to-help" {{ old('template') === 'how-to-help' ? 'selected' : '' }}>How to Help</option>
                            </select>
                            @if($errors->first('template'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('template') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Page</label>
                            <select name="parent_id" id="parent_id"
                                    class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                                <option value="">None (top-level page)</option>
                                @foreach($pages as $parentPage)
                                    <option value="{{ $parentPage->id }}" {{ old('parent_id') == $parentPage->id ? 'selected' : '' }}>
                                        {{ $parentPage->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->first('parent_id'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('parent_id') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="flex items-center">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                   {{ old('is_published', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="is_published" class="ml-2 text-sm text-gray-700">Published</label>
                        </div>
                        <div class="flex items-center">
                            <input type="hidden" name="show_in_nav" value="0">
                            <input type="checkbox" name="show_in_nav" id="show_in_nav" value="1"
                                   {{ old('show_in_nav') ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="show_in_nav" class="ml-2 text-sm text-gray-700">Show in Navigation</label>
                        </div>
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" min="0"
                                   value="{{ old('sort_order', 0) }}"
                                   class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                            @if($errors->first('sort_order'))
                                <p class="mt-1 text-sm text-red-600">{{ $errors->first('sort_order') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">SEO Settings</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" maxlength="60"
                                       value="{{ old('meta_title') }}"
                                       class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                                @if($errors->first('meta_title'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('meta_title') }}</p>
                                @endif
                                <p class="mt-1 text-xs text-gray-500">Recommended: 50-60 characters. Leave blank to use the page title.</p>
                            </div>
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                                          class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">{{ old('meta_description') }}</textarea>
                                @if($errors->first('meta_description'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('meta_description') }}</p>
                                @endif
                                <p class="mt-1 text-xs text-gray-500">Recommended: 150-160 characters.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.pages.index') }}" class="btn-admin-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Pages
                        </a>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-save mr-2"></i>Create Page
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function(e) {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    });

    // TinyMCE initialization for content editor
    tinymce.init({
        selector: '#content-editor',
        height: 400,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image | code | help',
        content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }'
    });
</script>
@endpush
