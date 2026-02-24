@component('mail::message')
# Reminder: {{ $rsvp->event->title }} is Tomorrow!

Hi {{ $rsvp->name }},

Just a friendly reminder that **{{ $rsvp->event->title }}** is happening tomorrow!

**When:** {{ $rsvp->event->starts_at->format('l, F j, Y \a\t g:i A') }}
@if($rsvp->event->location_name)
**Where:** {{ $rsvp->event->location_name }}
@endif

@if($rsvp->event->what_to_bring)
**Don't forget to bring:**
{{ $rsvp->event->what_to_bring }}
@endif

@component('mail::button', ['url' => route('events.show', $rsvp->event)])
View Event Details
@endcomponent

Can't make it? No worries — you can cancel your RSVP below.

@component('mail::button', ['url' => route('events.rsvp.cancel', $rsvp->token), 'color' => 'red'])
Cancel RSVP
@endcomponent

{{ config('app.name') }}
@endcomponent
