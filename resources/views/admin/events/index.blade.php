@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events</h1>
                <p class="text-gray-600 mt-1">Manage community events and RSVPs</p>
            </div>
            <div>
                <a href="{{ route('admin.events.create') }}" class="btn-admin-primary">
                    <i class="fas fa-plus mr-2"></i>Create Event
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            @if($events->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RSVPs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-sm font-semibold text-gray-900 hover:text-admin-teal">
                                            {{ $event->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $event->starts_at->format('M j, Y \a\t g:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $event->location_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $event->rsvps_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.events.edit', $event) }}"
                                           aria-label="Edit event"
                                           class="text-admin-teal hover:text-admin-teal/80 mr-3">
                                            <i class="fas fa-edit" aria-hidden="true"></i>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    aria-label="Delete event"
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <i class="fas fa-calendar-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No events found</h3>
                    <p class="text-gray-500 mb-4">Create your first event to get started.</p>
                    <a href="{{ route('admin.events.create') }}" class="btn-admin-primary">
                        <i class="fas fa-plus mr-2"></i>Create Event
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
