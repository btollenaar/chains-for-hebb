{{-- Cookie Consent Banner --}}
<div x-data="{
        show: false,
        init() {
            if (!localStorage.getItem('cookie_consent')) {
                this.show = true;
            }
        },
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.show = false;
        },
        decline() {
            localStorage.setItem('cookie_consent', 'declined');
            this.show = false;
        }
    }"
    x-cloak
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-y-full opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-full opacity-0"
    class="fixed bottom-0 inset-x-0 z-50 p-4 sm:p-6"
    role="dialog"
    aria-label="Cookie consent"
>
    <div class="max-w-4xl mx-auto card-glass rounded-2xl p-5 sm:p-6 shadow-2xl border" style="border-color: var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(20px);">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-cookie-bite text-earth-amber"></i>
                    <h3 class="font-semibold font-display" style="color: var(--on-surface);">We use cookies</h3>
                </div>
                <p class="text-sm" style="color: var(--on-surface-muted);">
                    We use cookies to improve your experience, analyze traffic, and personalize content. By clicking "Accept", you consent to our use of cookies. Read our
                    <a href="{{ route('legal.privacy-policy') }}" class="underline font-medium" style="color: #FF3366;">Privacy Policy</a>.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button @click="decline()" class="btn-glass px-4 py-2 rounded-xl text-sm font-semibold" style="color: var(--on-surface);">
                    Decline
                </button>
                <button @click="accept()" class="btn-gradient px-5 py-2 rounded-xl text-sm font-semibold">
                    Accept
                </button>
            </div>
        </div>
    </div>
</div>
