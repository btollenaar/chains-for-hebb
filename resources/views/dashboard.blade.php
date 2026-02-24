<x-app-layout>
    @section('title', 'Dashboard')
    @section('meta_description', 'Your account dashboard - manage orders and profile.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Welcome Back</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">Dashboard</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Hello, {{ Auth::user()->name }}!</p>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10" data-animate="stagger">
                @if(config('business.features.products'))
                <a href="{{ route('products.index') }}" class="card-glass rounded-2xl p-6 group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-earth-primary/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shopping-bag text-earth-primary text-xl"></i>
                    </div>
                    <h3 class="font-display font-bold mb-1" style="color: var(--on-surface);">Shop Products</h3>
                    <p class="text-sm" style="color: var(--on-surface-muted);">Browse our catalog</p>
                </a>
                @endif

                <a href="{{ route('cart.index') }}" class="card-glass rounded-2xl p-6 group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-earth-success/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shopping-cart text-earth-success text-xl"></i>
                    </div>
                    <h3 class="font-display font-bold mb-1" style="color: var(--on-surface);">My Cart</h3>
                    <p class="text-sm" style="color: var(--on-surface-muted);">View your cart</p>
                </a>

                <a href="{{ route('loyalty.index') }}" class="card-glass rounded-2xl p-6 group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-earth-amber/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-coins text-earth-amber text-xl"></i>
                    </div>
                    <h3 class="font-display font-bold mb-1" style="color: var(--on-surface);">Loyalty Points</h3>
                    <p class="text-sm" style="color: var(--on-surface-muted);">{{ number_format(Auth::user()->loyalty_points_balance) }} points</p>
                </a>

                <a href="{{ route('addresses.index') }}" class="card-glass rounded-2xl p-6 group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-earth-copper/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-map-marker-alt text-earth-copper text-xl"></i>
                    </div>
                    <h3 class="font-display font-bold mb-1" style="color: var(--on-surface);">Manage Addresses</h3>
                    <p class="text-sm" style="color: var(--on-surface-muted);">Saved addresses</p>
                </a>

                <a href="#" onclick="event.preventDefault(); document.getElementById('export-form').submit();" class="card-glass rounded-2xl p-6 group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-earth-sage/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-download text-earth-sage text-xl"></i>
                    </div>
                    <h3 class="font-display font-bold mb-1" style="color: var(--on-surface);">Export My Data</h3>
                    <p class="text-sm" style="color: var(--on-surface-muted);">Download your data (GDPR)</p>
                </a>
                <form id="export-form" action="{{ route('data-export.request') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>

            {{-- Account Info Card --}}
            <div class="card-glass rounded-2xl p-8" data-animate="fade-up">
                <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Account Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Name</p>
                        <p class="font-medium" style="color: var(--on-surface);">{{ Auth::user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Email</p>
                        <p class="font-medium" style="color: var(--on-surface);">{{ Auth::user()->email }}</p>
                    </div>
                    @if(Auth::user()->phone)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Phone</p>
                        <p class="font-medium" style="color: var(--on-surface);">{{ Auth::user()->phone }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--on-surface-muted);">Member Since</p>
                        <p class="font-medium" style="color: var(--on-surface);">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
