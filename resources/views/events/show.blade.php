<x-app-layout>
    @section('title', $event->title)

    <section class="py-16 px-4" style="background-color: var(--surface);">
        <div class="max-w-3xl mx-auto">
            @if($event->featured_image)
                <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover rounded-2xl mb-8">
            @endif

            <div class="mb-6">
                <span class="badge-gradient text-xs mb-3 inline-block">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                <h1 class="page-heading mb-4" style="color: var(--on-surface);">{{ $event->title }}</h1>
            </div>

            <div class="card-bento p-6 mb-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><i class="fas fa-calendar mr-2" style="color: var(--color-forest);"></i> {{ $event->starts_at->format('l, F j, Y') }}</div>
                    <div><i class="fas fa-clock mr-2" style="color: var(--color-forest);"></i> {{ $event->starts_at->format('g:i A') }}@if($event->ends_at) — {{ $event->ends_at->format('g:i A') }}@endif</div>
                    @if($event->location_name)
                    <div><i class="fas fa-map-marker-alt mr-2" style="color: var(--color-forest);"></i> {{ $event->location_name }}</div>
                    @endif
                    @if($event->max_attendees)
                    <div><i class="fas fa-users mr-2" style="color: var(--color-forest);"></i> {{ $event->spots_remaining ?? 'Unlimited' }} spots remaining</div>
                    @endif
                </div>
            </div>

            @if($event->content)
            <div class="prose max-w-none mb-8" style="color: var(--on-surface);">
                {!! $event->content !!}
            </div>
            @endif

            @if($event->what_to_bring)
            <div class="card-bento p-6 mb-8">
                <h3 class="font-display text-lg font-bold mb-2" style="color: var(--on-surface);">What to Bring</h3>
                <p style="color: var(--on-surface-muted);">{{ $event->what_to_bring }}</p>
            </div>
            @endif

            {{-- RSVP Form --}}
            @if(!$event->is_cancelled && (!$event->rsvp_deadline || $event->rsvp_deadline->isFuture()))
            <div class="card-bento p-8" id="rsvp">
                <h3 class="font-display text-xl font-bold mb-6 text-center" style="color: var(--on-surface);">RSVP</h3>
                <form action="{{ route('events.rsvp', $event) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Name</label>
                            <input type="text" name="name" required class="input-glass" value="{{ old('name', auth()->user()?->name) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Email</label>
                            <input type="email" name="email" required class="input-glass" value="{{ old('email', auth()->user()?->email) }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Party Size</label>
                        <select name="party_size" class="input-glass">
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--on-surface);">Notes (optional)</label>
                        <textarea name="notes" rows="2" class="input-glass" placeholder="Any questions or special requests?">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-gradient w-full">Confirm RSVP</button>
                </form>
            </div>
            @elseif($event->is_cancelled)
            <div class="card-bento p-8 text-center">
                <p class="text-red-600 font-semibold">This event has been cancelled.</p>
            </div>
            @endif
        </div>
    </section>
</x-app-layout>
