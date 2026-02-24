<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService
    ) {}

    /**
     * List upcoming events.
     */
    public function index()
    {
        $events = Event::published()
            ->upcoming()
            ->withCount(['rsvps' => fn ($q) => $q->confirmed()])
            ->orderBy('starts_at')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Show a single event.
     */
    public function show(Event $event)
    {
        if (!$event->is_published) {
            abort(404);
        }

        $event->loadCount(['rsvps' => fn ($q) => $q->confirmed()]);

        return view('events.show', compact('event'));
    }

    /**
     * Handle RSVP submission.
     */
    public function rsvp(Request $request, Event $event)
    {
        if ($event->is_cancelled) {
            return back()->with('error', 'This event has been cancelled.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'party_size' => 'nullable|integer|min:1|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['customer_id'] = Auth::id();

        $rsvp = $this->eventService->createRsvp($event, $validated);

        $message = $rsvp->status === 'waitlisted'
            ? 'You\'ve been added to the waitlist. We\'ll notify you if a spot opens up.'
            : 'Your RSVP is confirmed! Check your email for details.';

        return back()->with('success', $message);
    }

    /**
     * Cancel RSVP via email link.
     */
    public function cancelRsvp(string $token)
    {
        $rsvp = $this->eventService->cancelRsvp($token);

        if (!$rsvp) {
            abort(404, 'RSVP not found.');
        }

        return view('events.rsvp-cancelled', compact('rsvp'));
    }
}
