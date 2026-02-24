<x-app-layout>
    @section('title', 'RSVP Cancelled')

    <section class="py-20 px-4" style="background-color: var(--surface);">
        <div class="max-w-lg mx-auto text-center">
            <h1 class="page-heading mb-4" style="color: var(--on-surface);">RSVP Cancelled</h1>
            <p class="mb-8" style="color: var(--on-surface-muted);">
                Your RSVP for <strong>{{ $rsvp->event->title }}</strong> has been cancelled.
            </p>
            <a href="{{ route('events.index') }}" class="btn-gradient">Browse Events</a>
        </div>
    </section>
</x-app-layout>
