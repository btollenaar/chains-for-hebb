<x-app-layout>
    @section('title', 'Gallery')

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">Photo Gallery</h1>
                <p style="color: var(--on-surface-muted);">See the park, the progress, and our community in action.</p>
            </div>

            @if($albums->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($albums as $album)
                <a href="{{ route('gallery.show', $album) }}" class="card-bento block group">
                    <div class="aspect-video rounded-xl overflow-hidden mb-4">
                        @if($album->cover_image)
                            <img src="{{ Storage::url($album->cover_image) }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="background-color: var(--surface-border);">
                                <i class="fas fa-images text-4xl" style="color: var(--on-surface-muted);"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-display text-lg font-bold mb-1" style="color: var(--on-surface);">{{ $album->title }}</h3>
                    <div class="text-sm" style="color: var(--on-surface-muted);">
                        {{ $album->photos_count }} photos
                        @if($album->album_date) &middot; {{ $album->album_date->format('M Y') }}@endif
                    </div>
                </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $albums->links() }}</div>
            @else
            <div class="text-center py-12">
                <p style="color: var(--on-surface-muted);">Photos coming soon!</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
