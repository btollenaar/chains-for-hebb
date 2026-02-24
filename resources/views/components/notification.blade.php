{{-- Global Notification Component --}}
{{-- Displays slide-down modal notifications for success/error messages --}}
{{-- Works with both AJAX responses and Laravel session flash messages --}}

<div x-data="notificationManager()"
     @notify.window="show($event.detail)"
     x-show="visible"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-full"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-full"
     class="fixed top-0 left-0 right-0 z-50 mx-auto max-w-7xl px-4 pt-4">

    <div :class="type === 'success'
            ? 'bg-earth-success/10 border-earth-success/30 text-earth-success'
            : 'bg-red-500/10 border-red-500/30 text-red-500'"
         class="border backdrop-blur-lg px-5 py-4 rounded-xl shadow-lg relative flex items-center justify-between"
         style="background-color: var(--glass-bg);"
         role="alert">
        <div class="flex items-center gap-3">
            <i :class="type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'" class="text-lg"></i>
            <span class="block sm:inline font-medium" x-text="message"></span>
        </div>
        <button @click="hide()"
                type="button"
                class="ml-4 opacity-50 hover:opacity-100 focus:outline-none transition-opacity"
                aria-label="Close notification">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
/**
 * Alpine.js Notification Manager Component
 * Handles display and auto-dismiss of slide-down notifications
 */
function notificationManager() {
    return {
        visible: false,
        message: '',
        type: 'success', // 'success' or 'error'
        timeout: null,

        /**
         * Show notification with message and type
         * @param {Object} data - {message: string, type: string}
         */
        show(data) {
            this.message = data.message;
            this.type = data.type || 'success';
            this.visible = true;

            // Clear existing timeout if notification already showing
            if (this.timeout) clearTimeout(this.timeout);

            // Auto-dismiss after 3 seconds
            this.timeout = setTimeout(() => {
                this.hide();
            }, 3000);
        },

        /**
         * Hide notification and clear timeout
         */
        hide() {
            this.visible = false;
            if (this.timeout) {
                clearTimeout(this.timeout);
                this.timeout = null;
            }
        }
    }
}
</script>
