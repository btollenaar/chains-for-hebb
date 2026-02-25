<x-app-layout>
    @section('title', 'Photo Gallery')

    {{-- Hero Banner --}}
    <section class="relative py-24 px-4 overflow-hidden" style="margin-top: -72px; padding-top: calc(72px + 4rem); background: linear-gradient(135deg, #2D5016, #1A1A2E);">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[var(--surface)]"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h1 class="font-display text-white text-fluid-4xl font-bold uppercase tracking-tight mb-4">Photo Gallery</h1>
            <p class="text-white/80 text-fluid-base max-w-2xl mx-auto">
                See the park, the progress, and our community in action.
            </p>
        </div>
    </section>

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            @if($albums->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8" data-animate="stagger">
                @foreach($albums as $album)
                <a href="{{ route('gallery.show', $album) }}" class="card-bento block group relative overflow-hidden">
                    <div class="aspect-video rounded-xl overflow-hidden mb-4 relative">
                        @if($album->cover_image)
                            <img src="{{ Storage::url($album->cover_image) }}" alt="{{ $album->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="background: var(--gradient-nature);">
                                <i class="fas fa-images text-4xl text-white/60"></i>
                            </div>
                        @endif
                        {{-- Hover overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                            <span class="text-white font-semibold text-sm">
                                <i class="fas fa-images mr-1"></i> View Album
                            </span>
                        </div>
                        {{-- Photo count badge --}}
                        <div class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-bold text-white" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
                            <i class="fas fa-camera mr-1"></i>{{ $album->photos_count }}
                        </div>
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
            <div class="text-center py-12 card-bento" data-animate="fade-in">
                <div class="text-4xl mb-4">
                    <i class="fas fa-camera" style="color: var(--on-surface-muted);"></i>
                </div>
                <p class="text-lg font-semibold mb-2" style="color: var(--on-surface);">Photos coming soon!</p>
                <p style="color: var(--on-surface-muted);">We'll be sharing park photos, event recaps, and construction progress here.</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
