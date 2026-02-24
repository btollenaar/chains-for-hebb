@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.gallery.index') }}" class="text-sm text-admin-teal hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Gallery Albums
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Album</h1>
        <p class="text-gray-600 mt-1">Update album details and manage photos</p>
    </div>

    <div class="max-w-3xl">
        {{-- Album Details Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Album Details</h2>

            <form action="{{ route('admin.gallery.update', $album) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title', $album->title) }}"
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
                           value="{{ old('slug', $album->slug) }}"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm"
                              placeholder="A brief description of this album...">{{ old('description', $album->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cover Image --}}
                <div class="mb-6">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Cover Image
                    </label>
                    @if($album->cover_image)
                        <div class="mb-3">
                            <img src="{{ Storage::url($album->cover_image) }}"
                                 alt="{{ $album->title }}"
                                 class="w-48 h-32 object-cover rounded-md border border-gray-200">
                            <p class="text-xs text-gray-500 mt-1">Current cover image</p>
                        </div>
                    @endif
                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Upload a new image to replace the current one. Max 5MB.</p>
                </div>

                {{-- Album Date --}}
                <div class="mb-6">
                    <label for="album_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Album Date
                    </label>
                    <input type="date" name="album_date" id="album_date"
                           value="{{ old('album_date', $album->album_date?->format('Y-m-d')) }}"
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
                                   {{ old('is_published', $album->is_published) ? 'checked' : '' }}
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
                               value="{{ old('sort_order', $album->sort_order) }}"
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
                        <i class="fas fa-save mr-2"></i>Update Album
                    </button>
                </div>
            </form>
        </div>

        {{-- Photo Upload --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Upload Photos</h2>

            <form action="{{ route('admin.gallery.photos.upload', $album) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="photos" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Photos
                    </label>
                    <input type="file" name="photos[]" id="photos" multiple accept="image/*"
                           class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                    @error('photos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Select one or more images. Max 10MB each.</p>
                </div>

                <div class="mb-4">
                    <label for="photo_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Photo Type
                    </label>
                    <select name="photo_type" id="photo_type"
                            class="w-full border-gray-300 focus:border-admin-teal focus:ring-admin-teal rounded-md shadow-sm">
                        <option value="during">During</option>
                        <option value="before">Before</option>
                        <option value="after">After</option>
                        <option value="event">Event</option>
                    </select>
                </div>

                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-upload mr-2"></i>Upload Photos
                </button>
            </form>
        </div>

        {{-- Existing Photos --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                Photos
                <span class="text-sm font-normal text-gray-500">({{ $album->photos->count() }})</span>
            </h2>

            @if($album->photos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($album->photos as $photo)
                        <div class="relative group rounded-lg overflow-hidden border border-gray-200">
                            <img src="{{ Storage::url($photo->thumbnail_path ?? $photo->file_path) }}"
                                 alt="{{ $photo->alt_text }}"
                                 class="w-full aspect-square object-cover">

                            {{-- Caption overlay --}}
                            @if($photo->caption)
                                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-2 py-1.5 truncate">
                                    {{ $photo->caption }}
                                </div>
                            @endif

                            {{-- Delete button overlay --}}
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('admin.gallery.photos.destroy', [$album, $photo]) }}" method="POST"
                                      onsubmit="return confirm('Delete this photo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors"
                                            title="Delete photo">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Photo type badge --}}
                            @if($photo->photo_type)
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white/90 text-gray-700 shadow-sm">
                                        {{ ucfirst($photo->photo_type) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-camera text-gray-400 text-4xl mb-3"></i>
                    <p class="text-gray-500">No photos in this album yet. Use the form above to upload some.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
