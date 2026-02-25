<x-app-layout>
    @section('title', 'Events & Work Parties')

    {{-- Hero Banner --}}
    <section class="relative py-24 px-4 overflow-hidden" style="margin-top: -72px; padding-top: calc(72px + 4rem); background: linear-gradient(135deg, #2D5016, #1A1A2E);">
        @if(file_exists(public_path('images/generated/events-hero-community.webp')))
        <div class="absolute inset-0" style="background-image: url('{{ asset('images/generated/events-hero-community.webp') }}'); background-size: cover; background-position: center; opacity: 0.25;"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[var(--surface)]"></div>
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            <h1 class="font-display text-white text-fluid-4xl font-bold uppercase tracking-tight mb-4">Events & Work Parties</h1>
            <p class="text-white/80 text-fluid-base max-w-2xl mx-auto">
                Join us to help build the course or just hang out and throw some discs.
            </p>
        </div>
    </section>

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto">
            @if($events->isNotEmpty())
                {{-- Next Event Highlight --}}
                @php $nextEvent = $events->first(); @endphp
                <div class="card-bento p-6 mb-12 border-l-4" style="border-left-color: var(--color-forest);" data-animate="fade-up">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-gradient text-xs">Up Next</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide badge-event-{{ $nextEvent->event_type }}">
                            {{ ucfirst(str_replace('_', ' ', $nextEvent->event_type)) }}
                        </span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-shrink-0 w-20 h-20 rounded-xl flex flex-col items-center justify-center text-white" style="background: var(--gradient-primary);">
                            <span class="text-2xl font-bold leading-none">{{ $nextEvent->starts_at->format('d') }}</span>
                            <span class="text-xs uppercase tracking-wide">{{ $nextEvent->starts_at->format('M') }}</span>
                        </div>
                        <div class="flex-1">
                            <h2 class="font-display text-2xl font-bold mb-1" style="color: var(--on-surface);">{{ $nextEvent->title }}</h2>
                            <div class="flex flex-wrap gap-4 text-sm" style="color: var(--on-surface-muted);">
                                <span><i class="fas fa-clock mr-1"></i>{{ $nextEvent->starts_at->format('g:i A') }}</span>
                                @if($nextEvent->location_name)
                                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $nextEvent->location_name }}</span>
                                @endif
                                @if($nextEvent->rsvps_count ?? false)
                                <span><i class="fas fa-users mr-1"></i>{{ $nextEvent->rsvps_count }} attending</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('events.show', $nextEvent) }}" class="btn-donate btn-lg flex-shrink-0">Details & RSVP</a>
                    </div>
                </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-animate="stagger">
                @foreach($events->skip(1) as $event)
                <div class="card-bento">
                    @if($event->featured_image)
                        <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover rounded-xl mb-4">
                    @endif
                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg flex flex-col items-center justify-center text-white text-sm" style="background: var(--gradient-primary);">
                            <span class="text-base font-bold leading-none">{{ $event->starts_at->format('d') }}</span>
                            <span class="text-[9px] uppercase">{{ $event->starts_at->format('M') }}</span>
                        </div>
                        <div>
                            <h3 class="font-display text-lg font-bold leading-tight" style="color: var(--on-surface);">{{ $event->title }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide badge-event-{{ $event->event_type }}">
                                    {{ ucfirst(str_replace('_', ' ', $event->event_type)) }}
                                </span>
                                @if($event->is_cancelled)
                                    <span class="bg-red-100 text-red-700 text-[10px] font-semibold px-2 py-0.5 rounded-full">Cancelled</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-sm mb-2" style="color: var(--on-surface-muted);">
                        <i class="fas fa-clock mr-1"></i> {{ $event->starts_at->format('l, M j \a\t g:i A') }}
                    </div>
                    @if($event->location_name)
                    <div class="text-sm mb-3" style="color: var(--on-surface-muted);">
                        <i class="fas fa-map-marker-alt mr-1"></i> {{ $event->location_name }}
                    </div>
                    @endif
                    <p class="text-sm mb-4 line-clamp-2" style="color: var(--on-surface-muted);">{{ $event->description }}</p>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('events.show', $event) }}" class="btn-gradient btn-sm">Details & RSVP</a>
                        @if($event->rsvps_count ?? false)
                        <span class="text-xs" style="color: var(--on-surface-muted);">
                            <i class="fas fa-users mr-1"></i>{{ $event->rsvps_count }} attending
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $events->links() }}</div>
            @else
            <div class="text-center py-12 card-bento" data-animate="fade-in">
                <div class="text-4xl mb-4">
                    <i class="fas fa-calendar-alt" style="color: var(--on-surface-muted);"></i>
                </div>
                <p class="text-lg font-semibold mb-2" style="color: var(--on-surface);">No upcoming events right now</p>
                <p style="color: var(--on-surface-muted);">Check back soon — we're always planning something!</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
