{{-- Email Capture Popup — glassmorphism slide-up offering WELCOME10 coupon --}}
<div x-data="{
        show: false,
        submitted: false,
        email: '',
        loading: false,
        message: '',
        coupon: 'WELCOME10',
        copied: false,
        init() {
            {{-- Don't show for logged-in subscribers or if already dismissed --}}
            @auth
                const alreadySubscribed = {{ \App\Models\NewsletterSubscription::where('email', auth()->user()->email)->where('is_active', true)->exists() ? 'true' : 'false' }};
                if (alreadySubscribed) return;
            @endauth

            const dismissed = localStorage.getItem('email_popup_dismissed');
            if (dismissed) {
                const dismissedAt = parseInt(dismissed, 10);
                const sevenDays = 7 * 24 * 60 * 60 * 1000;
                if (Date.now() - dismissedAt < sevenDays) return;
            }

            setTimeout(() => { this.show = true; }, 5000);
        },
        dismiss() {
            this.show = false;
            localStorage.setItem('email_popup_dismissed', Date.now().toString());
        },
        async submit() {
            if (!this.email || this.loading) return;
            this.loading = true;

            try {
                const res = await fetch('{{ route('newsletter.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ email: this.email }),
                });
                const data = await res.json();

                if (data.success) {
                    this.submitted = true;
                    this.message = data.message;
                    this.coupon = data.coupon || 'WELCOME10';
                    localStorage.setItem('email_popup_dismissed', Date.now().toString());
                } else {
                    this.message = data.message || 'Something went wrong. Please try again.';
                }
            } catch {
                this.message = 'Something went wrong. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        copyCode() {
            navigator.clipboard.writeText(this.coupon);
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2000);
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
    @keydown.escape.window="dismiss()"
    class="fixed bottom-4 right-4 z-50 w-full max-w-sm sm:bottom-6 sm:right-6"
    role="dialog"
    aria-label="Subscribe for discount"
>
    <div class="card-glass rounded-2xl p-6 shadow-2xl border" style="border-color: var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(20px);">
        {{-- Close button --}}
        <button @click="dismiss()" class="absolute top-3 right-3 p-1 rounded-full transition-colors" style="color: var(--on-surface-muted);" aria-label="Close popup">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Pre-submit state --}}
        <template x-if="!submitted">
            <div>
                {{-- Icon --}}
                <div class="flex items-center justify-center w-12 h-12 rounded-xl mb-4" style="background: linear-gradient(135deg, #FF3366, #E62E5C);">
                    <i class="fas fa-tag text-white text-lg"></i>
                </div>

                <h3 class="text-lg font-bold font-display mb-1" style="color: var(--on-surface);">Get 10% Off</h3>
                <p class="text-sm mb-4" style="color: var(--on-surface-muted);">
                    Subscribe for updates and an exclusive discount on your first order.
                </p>

                <form @submit.prevent="submit()" class="space-y-3">
                    <input
                        type="email"
                        x-model="email"
                        placeholder="Your email address"
                        required
                        class="w-full px-4 py-2.5 rounded-xl text-sm transition-all"
                        style="background: var(--surface-raised); color: var(--on-surface); border: 1px solid var(--glass-border);"
                    >
                    <button
                        type="submit"
                        :disabled="loading"
                        class="btn-gradient w-full py-2.5 rounded-xl text-sm font-semibold"
                    >
                        <span x-show="!loading">Claim My 10% Off</span>
                        <span x-show="loading"><i class="fas fa-spinner fa-spin mr-1"></i> Subscribing...</span>
                    </button>
                </form>

                <p x-show="message && !submitted" x-text="message" class="text-xs mt-2 text-red-500"></p>
                <p class="text-xs mt-3" style="color: var(--on-surface-muted); opacity: 0.7;">
                    No spam. Unsubscribe anytime.
                </p>
            </div>
        </template>

        {{-- Post-submit success state --}}
        <template x-if="submitted">
            <div class="text-center">
                <div class="flex items-center justify-center w-12 h-12 rounded-full mx-auto mb-3" style="background: rgba(16, 185, 129, 0.15);">
                    <i class="fas fa-check text-earth-success text-xl"></i>
                </div>

                <h3 class="text-lg font-bold font-display mb-2" style="color: var(--on-surface);">You're In!</h3>
                <p class="text-sm mb-4" style="color: var(--on-surface-muted);" x-text="message"></p>

                {{-- Coupon code box --}}
                <div class="flex items-center justify-between gap-2 px-4 py-3 rounded-xl mb-4" style="background: var(--surface-raised); border: 2px dashed #FF3366;">
                    <span class="font-mono font-bold tracking-wider" style="color: #FF3366;" x-text="coupon"></span>
                    <button @click="copyCode()" class="text-xs font-semibold px-3 py-1 rounded-lg transition-colors" style="background: #FF3366; color: white;">
                        <span x-show="!copied">Copy</span>
                        <span x-show="copied">Copied!</span>
                    </button>
                </div>

                <a href="{{ route('products.index') }}?promo=WELCOME10" @click="dismiss()" class="btn-gradient inline-block w-full py-2.5 rounded-xl text-sm font-semibold text-center">
                    Start Shopping
                </a>
            </div>
        </template>
    </div>
</div>
