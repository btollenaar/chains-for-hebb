@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.gallery.index') }}" class="text-sm text-admin-teal hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Gallery Albums
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Create Album</h1>
        <p class="text-gray-600 mt-1">Add a new photo album to the gallery</p>
    </div>

    <div class="max-w-3xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Title --}}
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title') }}"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug
                    </label>
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug') }}"
                           placeholder="auto-generated-from-title"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Leave blank to auto-generate from the title.</p>
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm"
                              placeholder="A brief description of this album...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cover Image --}}
                <div class="mb-6">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Cover Image
                    </label>
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Recommended: landscape image, max 5MB.</p>
                </div>

                {{-- Album Date --}}
                <div class="mb-6">
                    <label for="album_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Album Date
                    </label>
                    <input type="date" name="album_date" id="album_date"
                           value="{{ old('album_date') }}"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('album_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Published --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Published</label>
                        <div class="flex items-center">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                   {{ old('is_published') ? 'checked' : '' }}
                                   class="h-4 w-4 text-admin-teal focus:ring-admin-teal border-gray-300 rounded">
                            <label for="is_published" class="ml-2 text-sm text-gray-700">
                                Make this album visible on the public gallery
                            </label>
                        </div>
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', 0) }}"
                               class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.gallery.index') }}" class="btn-admin-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn-admin-primary">
                        <i class="fas fa-save mr-2"></i>Create Album
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('title').addEventListener('input', function(e) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });
    </script>
    @endpush
@endsection
