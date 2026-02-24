<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Mail\EventRsvpConfirmationMail;
use App\Mail\EventReminderMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventService
{
    /**
     * Create an RSVP for an event.
     */
    public function createRsvp(Event $event, array $data): EventRsvp
    {
        // Check if already RSVP'd
        $existing = EventRsvp::where('event_id', $event->id)
            ->where('email', $data['email'])
            ->whereIn('status', ['confirmed', 'waitlisted'])
            ->first();

        if ($existing) {
            return $existing;
        }

        // Determine status (waitlist if full)
        $status = ($event->is_full) ? 'waitlisted' : 'confirmed';

        $rsvp = EventRsvp::create([
            'event_id' => $event->id,
            'customer_id' => $data['customer_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'party_size' => $data['party_size'] ?? 1,
            'status' => $status,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->sendConfirmationEmail($rsvp);

        return $rsvp;
    }

    /**
     * Cancel an RSVP by token.
     */
    public function cancelRsvp(string $token): ?EventRsvp
    {
        $rsvp = EventRsvp::where('token', $token)->first();

        if (!$rsvp) {
            return null;
        }

        $rsvp->update(['status' => 'cancelled']);

        // Check waitlist — promote next person
        $this->promoteFromWaitlist($rsvp->event);

        return $rsvp;
    }

    /**
     * Promote first waitlisted RSVP to confirmed.
     */
    protected function promoteFromWaitlist(Event $event): void
    {
        if ($event->is_full) {
            return;
        }

        $waitlisted = EventRsvp::where('event_id', $event->id)
            ->where('status', 'waitlisted')
            ->orderBy('created_at')
            ->first();

        if ($waitlisted) {
            $waitlisted->update(['status' => 'confirmed']);
            $this->sendConfirmationEmail($waitlisted);
        }
    }

    /**
     * Send reminder emails for events happening tomorrow.
     */
    public function sendReminders(): int
    {
        $tomorrow = now()->addDay();
        $count = 0;

        $events = Event::published()
            ->whereDate('starts_at', $tomorrow->toDateString())
            ->whereNull('cancelled_at')
            ->get();

        foreach ($events as $event) {
            $rsvps = $event->rsvps()->confirmed()->whereNull('reminder_sent_at')->get();

            foreach ($rsvps as $rsvp) {
                try {
                    Mail::to($rsvp->email)->send(new EventReminderMail($rsvp));
                    $rsvp->update(['reminder_sent_at' => now()]);
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Failed to send event reminder to {$rsvp->email}: {$e->getMessage()}");
                }
            }
        }

        return $count;
    }

    /**
     * Get upcoming events with RSVP counts.
     */
    public function getUpcoming(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Event::published()
            ->upcoming()
            ->withCount(['rsvps' => fn ($q) => $q->confirmed()])
            ->orderBy('starts_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get calendar data for display.
     */
    public function getCalendarData(int $month = null, int $year = null): array
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $events = Event::published()
            ->whereMonth('starts_at', $month)
            ->whereYear('starts_at', $year)
            ->orderBy('starts_at')
            ->get();

        return [
            'month' => $month,
            'year' => $year,
            'events' => $events,
        ];
    }

    protected function sendConfirmationEmail(EventRsvp $rsvp): void
    {
        try {
            Mail::to($rsvp->email)->send(new EventRsvpConfirmationMail($rsvp));
        } catch (\Exception $e) {
            Log::error("Failed to send RSVP confirmation to {$rsvp->email}: {$e->getMessage()}");
        }
    }
}
