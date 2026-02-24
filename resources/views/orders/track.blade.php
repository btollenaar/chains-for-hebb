<x-app-layout>
    @section('title', 'Track Your Order')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8" data-animate="fade-up">
                <div class="w-16 h-16 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-2xl text-earth-primary"></i>
                </div>
                <h1 class="text-fluid-2xl font-display font-bold mb-2" style="color: var(--on-surface);">Track Your Order</h1>
                <p style="color: var(--on-surface-muted);">Enter your order number and email to track your package.</p>
            </div>

            <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                <form method="POST" action="{{ route('track.lookup') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="order_number" class="block text-sm font-semibold mb-2" style="color: var(--on-surface);">Order Number</label>
                        <input type="text" name="order_number" id="order_number" value="{{ old('order_number') }}" required
                            placeholder="e.g. ORD-00001"
                            class="glass-input w-full px-4 py-3 rounded-xl" style="color: var(--on-surface);">
                        @error('order_number')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--on-surface);">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            placeholder="your@email.com"
                            class="glass-input w-full px-4 py-3 rounded-xl" style="color: var(--on-surface);">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full btn-gradient py-3 rounded-xl text-center">
                        <i class="fas fa-search mr-2"></i>Track Order
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
