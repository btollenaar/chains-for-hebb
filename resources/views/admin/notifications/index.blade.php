@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-1">{{ $unreadCount }} unread notification{{ $unreadCount !== 1 ? 's' : '' }}</p>
            </div>
            @if($unreadCount > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-admin-secondary">
                        <i class="fas fa-check-double mr-2"></i>Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-4xl mx-auto">
            @if($notifications->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No notifications</h3>
                    <p class="text-gray-500">You'll receive notifications here when new orders, appointments, or reviews come in.</p>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = $notification->read_at === null;
                            $colorClasses = [
                                'blue' => 'bg-blue-100 text-blue-600',
                                'green' => 'bg-green-100 text-green-600',
                                'yellow' => 'bg-yellow-100 text-yellow-600',
                                'red' => 'bg-red-100 text-red-600',
                            ];
                            $colorClass = $colorClasses[$data['color'] ?? 'gray'] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <a href="{{ route('admin.notifications.mark-read', $notification->id) }}"
                           class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-colors {{ $isUnread ? 'bg-blue-50/30' : '' }}">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $colorClass }}">
                                <i class="{{ $data['icon'] ?? 'fas fa-bell' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-900 {{ $isUnread ? '' : 'font-normal' }}">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </p>
                                    @if($isUnread)
                                        <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
