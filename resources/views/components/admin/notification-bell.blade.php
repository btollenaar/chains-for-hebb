<div x-data="notificationBell()" x-init="fetchNotifications(); startPolling()" class="relative">
    {{-- Bell Button --}}
    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors" aria-label="Notifications" aria-haspopup="true" :aria-expanded="open">
        <i class="fas fa-bell text-lg"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
              x-transition
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto bg-white rounded-lg shadow-xl border border-gray-200 z-50">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Notifications</h3>
            <button x-show="unreadCount > 0" @click.stop="markAllRead()" class="text-xs text-admin-teal hover:underline">
                Mark all read
            </button>
        </div>

        {{-- Notification List --}}
        <div>
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-bell-slash text-2xl mb-2 text-gray-300"></i>
                    <p>No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <a :href="'{{ route('admin.notifications.mark-read', '__ID__') }}'.replace('__ID__', notification.id)"
                   class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100"
                   :class="{ 'bg-blue-50/50': !notification.read }">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                             :class="{
                                 'bg-blue-100 text-blue-600': notification.color === 'blue',
                                 'bg-green-100 text-green-600': notification.color === 'green',
                                 'bg-yellow-100 text-yellow-600': notification.color === 'yellow',
                                 'bg-red-100 text-red-600': notification.color === 'red',
                                 'bg-gray-100 text-gray-600': !['blue','green','yellow','red'].includes(notification.color),
                             }">
                            <i :class="notification.icon" class="text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 mt-1" x-text="notification.time"></p>
                        </div>
                        <div x-show="!notification.read" class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2 border-t border-gray-200 text-center">
            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-admin-teal hover:underline font-medium">
                View All Notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        notifications: [],
        unreadCount: 0,
        pollingInterval: null,

        fetchNotifications() {
            fetch('{{ route('admin.notifications.recent') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.notifications = data.notifications;
                this.unreadCount = data.unread_count;
            })
            .catch(() => {});
        },

        startPolling() {
            this.pollingInterval = setInterval(() => this.fetchNotifications(), 30000);
        },

        markAllRead() {
            fetch('{{ route('admin.notifications.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(() => {
                this.notifications = this.notifications.map(n => ({ ...n, read: true }));
                this.unreadCount = 0;
            })
            .catch(() => {});
        }
    };
}
</script>
