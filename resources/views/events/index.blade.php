<x-app-layout>
    @section('title', 'Events')

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="page-heading text-gradient-nature mb-4">Events & Work Parties</h1>
                <p style="color: var(--on-surface-muted);" class="text-fluid-base">Join us to help build the course or just hang out and throw some discs.</p>
            </div>

            @if($events->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($events as $event)
                <div class="card-bento">
                    @if($event->featured_image)
                        <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover rounded-xl mb-4">
                    @endif
                    <div class="flex items-center gap-2 mb-3">
                        <span class="badge-gradient text-xs">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                        @if($event->is_cancelled)
                            <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Cancelled</span>
                        @endif
                    </div>
                    <h3 class="font-display text-xl font-bold mb-2" style="color: var(--on-surface);">{{ $event->title }}</h3>
                    <div class="text-sm mb-2" style="color: var(--on-surface-muted);">
                        <i class="fas fa-calendar mr-1"></i> {{ $event->starts_at->format('l, M j, Y \a\t g:i A') }}
                    </div>
                    @if($event->location_name)
                    <div class="text-sm mb-3" style="color: var(--on-surface-muted);">
                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $event->location_name }}
                    </div>
                    @endif
                    <p class="text-sm mb-4 line-clamp-2" style="color: var(--on-surface-muted);">{{ $event->description }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('events.show', $event) }}" class="btn-gradient btn-sm">Details & RSVP</a>
                        @if($event->rsvps_count !== null)
                        <span class="text-sm" style="color: var(--on-surface-muted);">{{ $event->rsvps_count }} attending</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $events->links() }}</div>
            @else
            <div class="text-center py-12">
                <p style="color: var(--on-surface-muted);">No upcoming events right now. Check back soon!</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
