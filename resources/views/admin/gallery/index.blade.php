@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gallery Albums</h1>
                <p class="text-gray-600 mt-1">Manage photo albums and galleries</p>
            </div>
            <a href="{{ route('admin.gallery.create') }}" class="btn-admin-primary">
                <i class="fas fa-plus mr-2"></i>Create Album
            </a>
        </div>
    </div>

    @if($albums->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($albums as $album)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Cover Image --}}
                    <div class="aspect-video bg-gray-100">
                        @if($album->cover_image)
                            <img src="{{ Storage::url($album->cover_image) }}"
                                 alt="{{ $album->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-images text-4xl mb-2"></i>
                                <span class="text-sm">No cover image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Album Info --}}
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $album->title }}</h3>
                            @if($album->is_published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0 ml-2">
                                    Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 flex-shrink-0 ml-2">
                                    Draft
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500 mb-4">
                            <span><i class="fas fa-camera mr-1"></i>{{ $album->photos_count }} {{ Str::plural('photo', $album->photos_count) }}</span>
                            @if($album->album_date)
                                <span class="ml-3"><i class="fas fa-calendar mr-1"></i>{{ $album->album_date->format('M j, Y') }}</span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('admin.gallery.edit', $album) }}" class="btn-admin-secondary text-sm flex-1 text-center">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <a href="{{ route('admin.gallery.show', $album) }}" class="btn-admin-secondary text-sm flex-1 text-center">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <form action="{{ route('admin.gallery.destroy', $album) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this album and all its photos? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 px-3 py-2 rounded-md hover:bg-red-50 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $albums->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center py-12">
                <i class="fas fa-images text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No albums yet</h3>
                <p class="text-gray-500 mb-6">Create your first photo album to start building your gallery.</p>
                <a href="{{ route('admin.gallery.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>Create Album
                </a>
            </div>
        </div>
    @endif
@endsection
