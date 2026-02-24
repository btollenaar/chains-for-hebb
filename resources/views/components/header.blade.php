<header
    x-data="{ scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled ? 'glass shadow-glass' : 'bg-transparent'"
>
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-[72px]">
            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center">
                    <x-application-logo class="h-10" />
                </a>
            </div>

            {{-- Desktop Navigation (centered) --}}
            <div class="hidden lg:flex items-center space-x-1">
                <a href="{{ route('home') }}"
                   class="nav-link {{ request()->routeIs('home') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Home
                </a>

                @if($featureSettings['products'] ?? true)
                <a href="{{ route('products.index') }}"
                   class="nav-link {{ request()->routeIs('products.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Shop
                </a>
                @endif

                @if($featureSettings['donations'] ?? false)
                <a href="{{ route('donate.index') }}"
                   class="nav-link {{ request()->routeIs('donate.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Donate
                </a>
                @endif

                @if($featureSettings['events'] ?? false)
                <a href="{{ route('events.index') }}"
                   class="nav-link {{ request()->routeIs('events.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Events
                </a>
                @endif

                @if($featureSettings['gallery'] ?? false)
                <a href="{{ route('gallery.index') }}"
                   class="nav-link {{ request()->routeIs('gallery.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Gallery
                </a>
                @endif

                @if($featureSettings['fundraising_tracker'] ?? false)
                <a href="{{ route('progress.index') }}"
                   class="nav-link {{ request()->routeIs('progress.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Progress
                </a>
                @endif

                {{-- CMS Pages with show_in_nav --}}
                @if($featureSettings['cms_pages'] ?? false)
                    @foreach(\App\Models\CmsPage::navPages()->get() as $navPage)
                    <a href="{{ route('pages.show', $navPage) }}"
                       class="nav-link {{ request()->is('pages/' . $navPage->slug) ? 'nav-link--active' : '' }}"
                       :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                        {{ $navPage->title }}
                    </a>
                    @endforeach
                @endif

                @if($featureSettings['blog'] ?? true)
                <a href="{{ route('blog.index') }}"
                   class="nav-link {{ request()->routeIs('blog.*') ? 'nav-link--active' : '' }}"
                   :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                    Blog
                </a>
                @endif

            </div>

            {{-- Right side: Search + Cart + Dark Mode + Auth --}}
            <div class="hidden lg:flex items-center space-x-2">
                {{-- Search --}}
                <div x-data="{
                        searchOpen: false,
                        searchQuery: '',
                        searchResults: { products: [], blog: [] },
                        searchLoading: false,
                        debounceTimer: null,
                        async doSearch() {
                            if (this.searchQuery.length < 2) {
                                this.searchResults = { products: [], blog: [] };
                                return;
                            }
                            this.searchLoading = true;
                            clearTimeout(this.debounceTimer);
                            this.debounceTimer = setTimeout(async () => {
                                try {
                                    const res = await fetch('/api/search/autocomplete?q=' + encodeURIComponent(this.searchQuery));
                                    this.searchResults = await res.json();
                                } catch (e) {
                                    this.searchResults = { products: [], blog: [] };
                                }
                                this.searchLoading = false;
                            }, 300);
                        },
                        get hasResults() {
                            return this.searchResults.products.length > 0 || this.searchResults.blog.length > 0;
                        },
                        submitSearch() {
                            if (this.searchQuery.length >= 2) {
                                window.location.href = '/search?q=' + encodeURIComponent(this.searchQuery);
                            }
                        }
                    }"
                    @click.away="searchOpen = false"
                    @keydown.escape="searchOpen = false"
                    class="relative">
                    <button @click="searchOpen = !searchOpen; $nextTick(() => { if (searchOpen) $refs.desktopSearchInput.focus() })"
                            class="p-2 rounded-lg transition-colors duration-200 hover:bg-white/10"
                            :class="scrolled ? 'text-[var(--on-surface)]' : 'text-white'"
                            aria-label="Search">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    {{-- Search Dropdown --}}
                    <div x-show="searchOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-0 mt-2 w-96 glass-card p-4 z-50"
                         style="display: none;">
                        <form @submit.prevent="submitSearch()">
                            <div class="relative">
                                <input type="text"
                                       x-ref="desktopSearchInput"
                                       x-model="searchQuery"
                                       @input="doSearch()"
                                       @keydown.enter.prevent="submitSearch()"
                                       placeholder="Search products, blog..."
                                       class="glass-input w-full pl-10 pr-4 py-2.5 rounded-xl text-sm"
                                       style="color: var(--on-surface);"
                                       autocomplete="off">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-earth-primary/60 text-sm"></i>
                                </div>
                                <div x-show="searchLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-spinner fa-spin text-earth-primary/60 text-sm"></i>
                                </div>
                            </div>
                        </form>

                        {{-- Autocomplete Results --}}
                        <div x-show="hasResults && searchQuery.length >= 2" class="mt-3 max-h-80 overflow-y-auto">
                            {{-- Products --}}
                            <template x-if="searchResults.products.length > 0">
                                <div class="mb-3">
                                    <p class="text-xs font-bold uppercase tracking-wider mb-2 px-1" style="color: var(--on-surface-muted);">Products</p>
                                    <template x-for="item in searchResults.products" :key="'p-' + item.id">
                                        <a :href="item.url" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-earth-primary/10 transition-colors">
                                            <img :src="item.image" :alt="item.name" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate" style="color: var(--on-surface);" x-text="item.name"></p>
                                                <p class="text-xs text-earth-primary font-semibold" x-text="'$' + Number(item.price).toFixed(2)"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            {{-- Blog --}}
                            <template x-if="searchResults.blog.length > 0">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider mb-2 px-1" style="color: var(--on-surface-muted);">Blog</p>
                                    <template x-for="item in searchResults.blog" :key="'b-' + item.id">
                                        <a :href="item.url" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-earth-primary/10 transition-colors">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: var(--surface-raised);">
                                                <i class="fas fa-newspaper text-sm" style="color: var(--on-surface-muted);"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium truncate" style="color: var(--on-surface);" x-text="item.name"></p>
                                                <p class="text-xs truncate" style="color: var(--on-surface-muted);" x-text="item.excerpt"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            {{-- View All Results Link --}}
                            <div class="mt-3 pt-3 border-t" style="border-color: var(--surface-border);">
                                <a :href="'/search?q=' + encodeURIComponent(searchQuery)" class="block text-center text-sm font-semibold text-earth-primary hover:underline py-1">
                                    View all results <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>

                        {{-- No Results --}}
                        <div x-show="!hasResults && searchQuery.length >= 2 && !searchLoading" class="mt-3 text-center py-4">
                            <p class="text-sm" style="color: var(--on-surface-muted);">No results found</p>
                        </div>
                    </div>
                </div>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   x-data="{ count: {{ $cartCount ?? 0 }} }"
                   @cart-updated.window="count = $event.detail.count"
                   class="relative p-2 rounded-lg transition-colors duration-200 hover:bg-white/10"
                   :class="scrolled ? 'text-[var(--on-surface)]' : 'text-white'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    <span x-show="count > 0"
                          x-text="count"
                          x-transition
                          class="absolute -top-0.5 -right-0.5 bg-earth-primary text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center"
                          aria-live="polite">
                    </span>
                </a>

                {{-- Dark Mode Toggle --}}
                <x-dark-mode-toggle />

                {{-- Auth --}}
                @auth
                    <div x-data="{ accountOpen: false }" class="relative">
                        <button @click="accountOpen = !accountOpen"
                                @click.away="accountOpen = false"
                                aria-haspopup="true"
                                :aria-expanded="accountOpen"
                                aria-label="Account menu"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                                :class="scrolled ? 'text-[var(--on-surface)] hover:bg-black/5 dark:hover:bg-white/10' : 'text-white hover:bg-white/10'">
                            <div class="w-7 h-7 rounded-full bg-earth-primary/20 flex items-center justify-center">
                                <i class="fas fa-user text-xs text-earth-primary"></i>
                            </div>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="accountOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="accountOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 mt-1 w-56 glass-card py-2 z-50"
                             style="display: none;">
                            <div class="px-4 py-2 border-b mb-1" style="border-color: var(--surface-border);">
                                <p class="text-sm font-semibold" style="color: var(--on-surface);">{{ auth()->user()->name }}</p>
                                <p class="text-xs" style="color: var(--on-surface-muted);">{{ auth()->user()->email }}</p>
                            </div>

                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors" style="color: var(--on-surface);">
                                    <i class="fas fa-tachometer-alt mr-2 w-4 text-center"></i>Admin Dashboard
                                </a>
                            @endif

                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors {{ request()->routeIs('dashboard') ? 'text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('dashboard') ? '' : 'color: var(--on-surface);' }}">
                                <i class="fas fa-home mr-2 w-4 text-center"></i>Overview
                            </a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors {{ request()->routeIs('orders.*') ? 'text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('orders.*') ? '' : 'color: var(--on-surface);' }}">
                                <i class="fas fa-shopping-bag mr-2 w-4 text-center"></i>Orders
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors {{ request()->routeIs('wishlist.*') ? 'text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('wishlist.*') ? '' : 'color: var(--on-surface);' }}">
                                <i class="fas fa-heart mr-2 w-4 text-center"></i>Wishlist
                            </a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-earth-primary/10 hover:text-earth-primary transition-colors {{ request()->routeIs('profile.*') ? 'text-earth-primary font-semibold' : '' }}" style="{{ request()->routeIs('profile.*') ? '' : 'color: var(--on-surface);' }}">
                                <i class="fas fa-user-cog mr-2 w-4 text-center"></i>Profile
                            </a>

                            <div class="my-1 mx-4 border-t" style="border-color: var(--surface-border);"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2 w-4 text-center"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="nav-link"
                       :class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn-gradient btn-sm">
                        Register
                    </a>
                @endauth
            </div>

            {{-- Mobile: Cart + Hamburger --}}
            <div class="flex items-center gap-2 lg:hidden">
                <a href="{{ route('cart.index') }}"
                   x-data="{ count: {{ $cartCount ?? 0 }} }"
                   @cart-updated.window="count = $event.detail.count"
                   class="relative p-2 rounded-lg"
                   :class="scrolled ? 'text-[var(--on-surface)]' : 'text-white'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    <span x-show="count > 0"
                          x-text="count"
                          class="absolute -top-0.5 -right-0.5 bg-earth-primary text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center"
                          aria-live="polite">
                    </span>
                </a>

                <button @click="$dispatch('mobile-nav-open')"
                        class="p-2 rounded-lg transition-colors duration-200"
                        :class="scrolled ? 'text-[var(--on-surface)] hover:bg-black/5 dark:hover:bg-white/10' : 'text-white hover:bg-white/10'"
                        aria-label="Open menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header>

{{-- Mobile Navigation Drawer --}}
<x-mobile-nav-drawer />
