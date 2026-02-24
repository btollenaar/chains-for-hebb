<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount(['rsvps' => fn ($q) => $q->confirmed()])
            ->orderByDesc('starts_at')
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120',
            'event_type' => 'required|in:work_party,fundraiser,meetup,tournament',
            'location_name' => 'nullable|string|max:255',
            'starts_at' => 'required|date|after:now',
            'ends_at' => 'nullable|date|after:starts_at',
            'max_attendees' => 'nullable|integer|min:1',
            'rsvp_deadline' => 'nullable|date|before:starts_at',
            'what_to_bring' => 'nullable|string|max:1000',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    public function show(Event $event)
    {
        $event->load(['rsvps' => fn ($q) => $q->orderBy('created_at', 'desc')]);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120',
            'event_type' => 'required|in:work_party,fundraiser,meetup,tournament',
            'location_name' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'max_attendees' => 'nullable|integer|min:1',
            'rsvp_deadline' => 'nullable|date|before:starts_at',
            'what_to_bring' => 'nullable|string|max:1000',
            'is_published' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    public function exportRsvps(Event $event)
    {
        $filename = 'rsvps-' . $event->slug . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($event) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Party Size', 'Status', 'Notes', 'RSVP Date']);

            foreach ($event->rsvps as $rsvp) {
                fputcsv($handle, [
                    $rsvp->name,
                    $rsvp->email,
                    $rsvp->party_size,
                    $rsvp->status,
                    $rsvp->notes,
                    $rsvp->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
