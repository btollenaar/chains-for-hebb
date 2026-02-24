@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.gallery.index') }}" class="text-sm text-admin-teal hover:underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Gallery Albums
        </a>
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mt-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $album->title }}</h1>
                @if($album->description)
                    <p class="text-gray-600 mt-1">{{ $album->description }}</p>
                @endif
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    @if($album->album_date)
                        <span><i class="fas fa-calendar mr-1"></i>{{ $album->album_date->format('M j, Y') }}</span>
                    @endif
                    <span><i class="fas fa-camera mr-1"></i>{{ $album->photos->count() }} {{ Str::plural('photo', $album->photos->count()) }}</span>
                    @if($album->is_published)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Draft
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.gallery.edit', $album) }}" class="btn-admin-primary flex-shrink-0">
                <i class="fas fa-edit mr-2"></i>Edit Album
            </a>
        </div>
    </div>

    {{-- Cover Image --}}
    @if($album->cover_image)
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Cover Image</p>
                <img src="{{ Storage::url($album->cover_image) }}"
                     alt="{{ $album->title }}"
                     class="w-full max-w-md rounded-md">
            </div>
        </div>
    @endif

    {{-- Photos Grid --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
            All Photos
        </h2>

        @if($album->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
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

                        {{-- Photo type badge --}}
                        @if($photo->photo_type)
                            <div class="absolute top-2 left-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white/90 text-gray-700 shadow-sm">
                                    {{ ucfirst($photo->photo_type) }}
                                </span>
                            </div>
                        @endif

                        {{-- Featured badge --}}
                        @if($photo->is_featured)
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-400 text-yellow-900 shadow-sm">
                                    <i class="fas fa-star text-xs"></i>
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-camera text-gray-400 text-5xl mb-3"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No photos yet</h3>
                <p class="text-gray-500 mb-4">This album doesn't have any photos. Add some from the edit page.</p>
                <a href="{{ route('admin.gallery.edit', $album) }}" class="btn-admin-primary">
                    <i class="fas fa-upload mr-2"></i>Upload Photos
                </a>
            </div>
        @endif
    </div>
@endsection
