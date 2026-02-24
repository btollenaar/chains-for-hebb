@component('mail::message')
# RSVP {{ $rsvp->status === 'waitlisted' ? 'Waitlisted' : 'Confirmed' }}

Hi {{ $rsvp->name }},

@if($rsvp->status === 'waitlisted')
You've been added to the waitlist for **{{ $rsvp->event->title }}**. We'll notify you if a spot opens up.
@else
Your RSVP for **{{ $rsvp->event->title }}** is confirmed!
@endif

**When:** {{ $rsvp->event->starts_at->format('l, F j, Y \a\t g:i A') }}
@if($rsvp->event->ends_at)
**Until:** {{ $rsvp->event->ends_at->format('g:i A') }}
@endif
@if($rsvp->event->location_name)
**Where:** {{ $rsvp->event->location_name }}
@endif
**Party Size:** {{ $rsvp->party_size }}

@if($rsvp->event->what_to_bring)
**What to Bring:**
{{ $rsvp->event->what_to_bring }}
@endif

@component('mail::button', ['url' => route('events.rsvp.cancel', $rsvp->token)])
Cancel RSVP
@endcomponent

See you there!

{{ config('app.name') }}
@endcomponent
