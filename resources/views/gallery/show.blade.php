<x-app-layout>
    @section('title', $album->title)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('gallery.index') }}" class="text-sm mb-2 inline-block" style="color: var(--color-forest);">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Gallery
                </a>
                <h1 class="page-heading mb-2" style="color: var(--on-surface);">{{ $album->title }}</h1>
                @if($album->description)
                <p style="color: var(--on-surface-muted);">{{ $album->description }}</p>
                @endif
            </div>

            @if($album->photos->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($album->photos as $photo)
                <div class="gallery-image-wrapper rounded-xl overflow-hidden aspect-square">
                    <a href="{{ Storage::url($photo->file_path) }}" class="glightbox" data-gallery="album" data-description="{{ $photo->caption }}">
                        <img src="{{ Storage::url($photo->thumbnail_path ?? $photo->file_path) }}"
                             alt="{{ $photo->alt_text }}"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        <span class="zoom-icon"><i class="fas fa-search-plus"></i></span>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center py-12" style="color: var(--on-surface-muted);">No photos in this album yet.</p>
            @endif
        </div>
    </section>
</x-app-layout>
