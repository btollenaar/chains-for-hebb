{{-- Mobile Navigation Drawer - Full viewport slide-out panel from right --}}
<div
    x-data="{ open: false }"
    @mobile-nav-open.window="open = true"
    @keydown.escape.window="open = false"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm"
        style="display: none;"
    ></div>

    {{-- Drawer Panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed top-0 right-0 z-[70] h-full w-80 max-w-[85vw] overflow-y-auto"
        style="display: none; background: var(--surface-raised);"
        @keydown.tab="$event.target.closest('[x-show]') || (open = false)"
    >
        {{-- Close button --}}
        <div class="flex items-center justify-between p-4 border-b" style="border-color: var(--surface-border);">
            <span class="font-display font-bold text-lg" style="color: var(--on-surface);">Menu</span>
            <button @click="open = false" class="p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/10 transition-colors" aria-label="Close menu">
                <svg class="w-6 h-6" style="color: var(--on-surface);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Dark mode toggle --}}
        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color: var(--surface-border);">
            <span class="text-sm font-medium" style="color: var(--on-surface-muted);">Dark Mode</span>
            <x-dark-mode-toggle />
        </div>

        {{-- Auth Section --}}
        <div class="px-4 py-3 border-b" style="border-color: var(--surface-border);">
            @auth
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-earth-primary/10 flex items-center justify-center">
                        <i class="fas fa-user text-earth-primary"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--on-surface);">{{ auth()->user()->name }}</p>
                        <p class="text-xs" style="color: var(--on-surface-muted);">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                {{-- Role-specific dashboard links --}}
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" @click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors" style="color: var(--on-surface);">
                        <i class="fas fa-tachometer-alt w-5 text-center text-earth-primary"></i> Admin Dashboard
                    </a>
                @endif

                {{-- Account links --}}
                <div class="mt-2 space-y-1">
                    <a href="{{ route('dashboard') }}" @click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard') ? 'font-semibold bg-earth-primary/10 text-earth-primary' : '' }}" style="{{ request()->routeIs('dashboard') ? '' : 'color: var(--on-surface);' }}">
                        <i class="fas fa-home w-5 text-center"></i> Overview
                    </a>
                    <a href="{{ route('orders.index') }}" @click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('orders.*') ? 'font-semibold bg-earth-primary/10 text-earth-primary' : '' }}" style="{{ request()->routeIs('orders.*') ? '' : 'color: var(--on-surface);' }}">
                        <i class="fas fa-shopping-bag w-5 text-center"></i> Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}" @click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('wishlist.*') ? 'font-semibold bg-earth-primary/10 text-earth-primary' : '' }}" style="{{ request()->routeIs('wishlist.*') ? '' : 'color: var(--on-surface);' }}">
                        <i class="fas fa-heart w-5 text-center"></i> Wishlist
                    </a>
                    <a href="{{ route('profile.edit') }}" @click="open = false" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('profile.*') ? 'font-semibold bg-earth-primary/10 text-earth-primary' : '' }}" style="{{ request()->routeIs('profile.*') ? '' : 'color: var(--on-surface);' }}">
                        <i class="fas fa-user-cog w-5 text-center"></i> Profile
                    </a>
                </div>
            @else
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" @click="open = false" class="btn-gradient btn-sm flex-1 text-center">Login</a>
                    <a href="{{ route('register') }}" @click="open = false" class="btn-outline-gradient btn-sm flex-1 text-center">Register</a>
                </div>
            @endauth
        </div>

        {{-- Mobile Search --}}
        <div class="px-4 py-3 border-b" style="border-color: var(--surface-border);">
            <form method="GET" action="{{ route('search') }}">
                <div class="relative">
                    <input type="text"
                           name="q"
                           placeholder="Search..."
                           class="glass-input w-full pl-10 pr-4 py-2.5 rounded-xl text-sm"
                           style="color: var(--on-surface);"
                           minlength="2"
                           required>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-earth-primary/60 text-sm"></i>
                    </div>
                </div>
            </form>
        </div>

        {{-- Navigation Links --}}
        <nav class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('home') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('home') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-home w-5 text-center"></i> Home
            </a>

            @if($featureSettings['products'] ?? true)
            <a href="{{ route('products.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('products.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('products.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-shopping-bag w-5 text-center"></i> Shop
            </a>
            @endif

            @if($featureSettings['donations'] ?? false)
            <a href="{{ route('donate.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('donate.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('donate.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-hand-holding-heart w-5 text-center"></i> Donate
            </a>
            @endif

            @if($featureSettings['events'] ?? false)
            <a href="{{ route('events.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('events.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('events.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-calendar-alt w-5 text-center"></i> Events
            </a>
            @endif

            @if($featureSettings['gallery'] ?? false)
            <a href="{{ route('gallery.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('gallery.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('gallery.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-images w-5 text-center"></i> Gallery
            </a>
            @endif

            @if($featureSettings['fundraising_tracker'] ?? false)
            <a href="{{ route('progress.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('progress.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('progress.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-chart-line w-5 text-center"></i> Progress
            </a>
            @endif

            @if($featureSettings['blog'] ?? true)
            <a href="{{ route('blog.index') }}" @click="open = false" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('blog.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('blog.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-newspaper w-5 text-center"></i> Blog
            </a>
            @endif

            <a href="{{ route('cart.index') }}" @click="open = false"
               x-data="{ count: {{ $cartCount ?? 0 }} }"
               @cart-updated.window="count = $event.detail.count"
               class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 transition-colors {{ request()->routeIs('cart.*') ? 'bg-earth-primary/10 text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('cart.*') ? '' : 'color: var(--on-surface);' }}">
                <i class="fas fa-shopping-cart w-5 text-center"></i>
                Cart
                <span x-show="count > 0" x-text="count" class="ml-auto bg-earth-primary text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center" aria-live="polite"></span>
            </a>
        </nav>

        {{-- Logout --}}
        @auth
        <div class="px-4 py-4 border-t mt-auto" style="border-color: var(--surface-border);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i> Logout
                </button>
            </form>
        </div>
        @endauth
    </div>
</div>
