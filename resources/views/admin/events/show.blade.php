@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @php
                        $typeBadges = [
                            'work_party' => 'bg-green-100 text-green-800',
                            'fundraiser' => 'bg-purple-100 text-purple-800',
                            'meetup' => 'bg-blue-100 text-blue-800',
                            'tournament' => 'bg-orange-100 text-orange-800',
                        ];
                        $typeLabels = [
                            'work_party' => 'Work Party',
                            'fundraiser' => 'Fundraiser',
                            'meetup' => 'Meetup',
                            'tournament' => 'Tournament',
                        ];
                        $badgeClass = $typeBadges[$event->event_type] ?? 'bg-gray-100 text-gray-800';
                        $typeLabel = $typeLabels[$event->event_type] ?? ucfirst($event->event_type);
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                        {{ $typeLabel }}
                    </span>
                    @if($event->is_cancelled)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Cancelled
                        </span>
                    @elseif($event->is_published)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Draft
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.events.edit', $event) }}" class="btn-admin-primary">
                    <i class="fas fa-edit mr-2"></i>Edit Event
                </a>
                <a href="{{ route('admin.events.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Events
                </a>
            </div>
        </div>
    </div>

    <!-- Event Details Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Event Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($event->featured_image)
                <div class="md:col-span-2">
                    <img src="{{ asset('storage/' . $event->featured_image) }}" alt="{{ $event->title }}"
                         class="w-full max-w-md h-48 object-cover rounded-lg shadow-sm">
                </div>
            @endif

            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Date & Time</p>
                <p class="mt-1 text-sm text-gray-900">{{ $event->starts_at->format('l, F j, Y \a\t g:i A') }}</p>
                @if($event->ends_at)
                    <p class="text-sm text-gray-600">Ends: {{ $event->ends_at->format('g:i A') }}</p>
                @endif
            </div>

            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Location</p>
                <p class="mt-1 text-sm text-gray-900">{{ $event->location_name ?? 'Not specified' }}</p>
            </div>

            @if($event->max_attendees)
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Capacity</p>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $event->rsvps->where('status', 'confirmed')->sum('party_size') }} / {{ $event->max_attendees }} spots filled
                        @if($event->spots_remaining !== null && $event->spots_remaining > 0)
                            <span class="text-green-600">({{ $event->spots_remaining }} remaining)</span>
                        @elseif($event->is_full)
                            <span class="text-red-600">(Full)</span>
                        @endif
                    </p>
                </div>
            @endif

            @if($event->rsvp_deadline)
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">RSVP Deadline</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $event->rsvp_deadline->format('l, F j, Y \a\t g:i A') }}</p>
                </div>
            @endif

            @if($event->description)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Description</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $event->description }}</p>
                </div>
            @endif

            @if($event->what_to_bring)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">What to Bring</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $event->what_to_bring }}</p>
                </div>
            @endif

            @if($event->content)
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium mb-1">Content</p>
                    <div class="prose prose-sm max-w-none text-gray-900">
                        {!! $event->content !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- RSVPs Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4 pb-2 border-b">
            <h2 class="text-lg font-bold text-gray-900">
                RSVPs
                <span class="text-sm font-normal text-gray-500">({{ $event->rsvps->count() }} total)</span>
            </h2>
            @if($event->rsvps->count() > 0)
                <a href="{{ route('admin.events.rsvps.export', $event) }}" class="btn-admin-secondary">
                    <i class="fas fa-download mr-2"></i>Export RSVPs
                </a>
            @endif
        </div>

        @if($event->rsvps->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Party Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($event->rsvps as $rsvp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $rsvp->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rsvp->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rsvp->party_size }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $rsvpStatusBadges = [
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'waitlisted' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $rsvpBadgeClass = $rsvpStatusBadges[$rsvp->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rsvpBadgeClass }}">
                                        {{ ucfirst($rsvp->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $rsvp->created_at->format('M j, Y \a\t g:i A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-users text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-500">No RSVPs yet for this event.</p>
            </div>
        @endif
    </div>
@endsection
