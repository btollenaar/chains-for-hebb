<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="{{ \App\Models\Setting::get('branding.favicon_path') ?: asset('favicon.ico') }}" type="image/x-icon">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/f90fa1caba.js" crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- TinyMCE WYSIWYG Editor -->
    <script src="https://cdn.tiny.cloud/1/kh3vhfgxdfo6kz7tzjfulah6hs735glyg7cr378gob5ljlg3/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{
    sidebarOpen: false,
    collapsed: localStorage.getItem('admin_sidebar_collapsed') === 'true',
    openGroups: {
        newsletter: {{ request()->routeIs('admin.newsletter.*') || request()->routeIs('admin.newsletters.*') || request()->routeIs('admin.subscriber-lists.*') ? 'true' : 'false' }},
        fundraising: {{ request()->routeIs('admin.donations.*') || request()->routeIs('admin.donation-tiers.*') || request()->routeIs('admin.fundraising.*') || request()->routeIs('admin.sponsors.*') || request()->routeIs('admin.sponsor-tiers.*') ? 'true' : 'false' }},
        community: {{ request()->routeIs('admin.events.*') || request()->routeIs('admin.gallery.*') ? 'true' : 'false' }}
    },
    toggleCollapse() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('admin_sidebar_collapsed', this.collapsed);
    },
    toggleGroup(name) {
        this.openGroups[name] = !this.openGroups[name];
    }
}">
    <div class="min-h-screen bg-gray-100 flex">

        {{-- ============================================================
             SIDEBAR - Desktop (expanded/collapsed) + Mobile (drawer)
             ============================================================ --}}

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"
             x-cloak></div>

        {{-- Sidebar panel --}}
        <aside :class="{
                   'translate-x-0': sidebarOpen,
                   '-translate-x-full lg:translate-x-0': !sidebarOpen,
                   'lg:w-64': !collapsed,
                   'lg:w-16': collapsed
               }"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-abs-primary text-gray-300 flex flex-col transition-all duration-300 ease-in-out lg:static lg:z-auto overflow-hidden">

            {{-- Sidebar header --}}
            <div class="flex items-center h-14 px-4 border-b border-gray-700 flex-shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 overflow-hidden group">
                    <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}" alt="{{ config('business.profile.name') }}" class="h-8 w-8 flex-shrink-0 object-contain transition-transform duration-200 group-hover:scale-105">
                    <div class="flex flex-col min-w-0" x-show="!collapsed" x-transition.opacity>
                        <span class="text-white font-bold text-sm leading-tight truncate">Admin Panel</span>
                        <span class="text-xs leading-tight truncate" style="color: #F2ECE4;">{{ config('business.profile.name') }}</span>
                    </div>
                </a>
            </div>

            {{-- Sidebar navigation --}}
            <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-1 sidebar-scrollbar">

                {{-- Dashboard & Analytics (no section header) --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'sidebar-nav-link--active' : '' }}"
                   :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                   :title="collapsed ? 'Dashboard' : ''">
                    <i class="fas fa-tachometer-alt sidebar-nav-icon"></i>
                    <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Dashboard</span>
                </a>
                <a href="{{ route('admin.analytics.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.analytics.*') ? 'sidebar-nav-link--active' : '' }}"
                   :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                   :title="collapsed ? 'Analytics' : ''">
                    <i class="fas fa-chart-line sidebar-nav-icon"></i>
                    <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Analytics</span>
                </a>

                {{-- CATALOG section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">Catalog</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.printful.catalog') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.printful.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Printful' : ''">
                        <i class="fas fa-tshirt sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Printful Catalog</span>
                    </a>
                    <a href="{{ route('admin.products.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.products.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Products' : ''">
                        <i class="fas fa-box sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Products</span>
                    </a>
                    <a href="{{ route('admin.tags.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.tags.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Tags' : ''">
                        <i class="fas fa-tags sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Tags</span>
                    </a>
                    <a href="{{ route('admin.imports.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.imports.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Import' : ''">
                        <i class="fas fa-file-import sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Import</span>
                    </a>
                </div>

                {{-- SALES section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">Sales</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.orders.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.orders.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Orders' : ''">
                        <i class="fas fa-shopping-bag sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Orders</span>
                    </a>
                    <a href="{{ route('admin.returns.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.returns.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Returns' : ''">
                        <i class="fas fa-undo sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Returns</span>
                    </a>
                    <a href="{{ route('admin.coupons.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.coupons.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Coupons' : ''">
                        <i class="fas fa-ticket-alt sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Coupons</span>
                    </a>
                </div>

                {{-- FUNDRAISING section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">Fundraising</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    {{-- Fundraising expandable group --}}
                    <div x-show="!collapsed">
                        <button @click="toggleGroup('fundraising')"
                                class="sidebar-nav-link w-full {{ request()->routeIs('admin.donations.*') || request()->routeIs('admin.donation-tiers.*') || request()->routeIs('admin.fundraising.*') || request()->routeIs('admin.sponsors.*') || request()->routeIs('admin.sponsor-tiers.*') ? 'sidebar-nav-link--active' : '' }}">
                            <i class="fas fa-hand-holding-heart sidebar-nav-icon"></i>
                            <span class="sidebar-nav-text flex-1 text-left">Fundraising</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openGroups.fundraising }"></i>
                        </button>
                        <div x-show="openGroups.fundraising" x-collapse class="space-y-0.5">
                            <a href="{{ route('admin.donations.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.donations.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-donate sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Donations</span>
                            </a>
                            <a href="{{ route('admin.donation-tiers.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.donation-tiers.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-layer-group sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Donation Tiers</span>
                            </a>
                            <a href="{{ route('admin.fundraising.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.fundraising.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-chart-line sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Progress</span>
                            </a>
                            <a href="{{ route('admin.sponsors.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.sponsors.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-handshake sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Sponsors</span>
                            </a>
                            <a href="{{ route('admin.sponsor-tiers.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.sponsor-tiers.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-medal sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Sponsor Tiers</span>
                            </a>
                        </div>
                    </div>
                    {{-- Fundraising collapsed icon --}}
                    <a href="{{ route('admin.donations.index') }}"
                       x-show="collapsed"
                       class="sidebar-nav-link sidebar-nav-link--collapsed {{ request()->routeIs('admin.donations.*') || request()->routeIs('admin.fundraising.*') || request()->routeIs('admin.sponsors.*') ? 'sidebar-nav-link--active' : '' }}"
                       title="Fundraising">
                        <i class="fas fa-hand-holding-heart sidebar-nav-icon"></i>
                    </a>
                </div>

                {{-- COMMUNITY section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">Community</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.events.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.events.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Events' : ''">
                        <i class="fas fa-calendar-alt sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Events</span>
                    </a>
                    <a href="{{ route('admin.gallery.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.gallery.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Gallery' : ''">
                        <i class="fas fa-images sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Gallery</span>
                    </a>
                </div>

                {{-- PEOPLE section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">People</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.customers.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.customers.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Customers' : ''">
                        <i class="fas fa-users sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Customers</span>
                    </a>
                    <a href="{{ route('admin.reviews.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.reviews.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Reviews' : ''">
                        <i class="fas fa-star sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Reviews</span>
                    </a>
                </div>

                {{-- CONTENT section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">Content</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.pages.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.pages.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Pages' : ''">
                        <i class="fas fa-file-alt sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Pages</span>
                    </a>
                    <a href="{{ route('admin.blog.posts.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.blog.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Blog' : ''">
                        <i class="fas fa-blog sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Blog</span>
                    </a>
                    <a href="{{ route('admin.about.edit') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.about.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'About' : ''">
                        <i class="fas fa-info-circle sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>About</span>
                    </a>

                    {{-- Newsletter expandable group --}}
                    <div x-show="!collapsed">
                        <button @click="toggleGroup('newsletter')"
                                class="sidebar-nav-link w-full {{ request()->routeIs('admin.newsletter.*') || request()->routeIs('admin.newsletters.*') || request()->routeIs('admin.subscriber-lists.*') ? 'sidebar-nav-link--active' : '' }}">
                            <i class="fas fa-envelope sidebar-nav-icon"></i>
                            <span class="sidebar-nav-text flex-1 text-left">Newsletter</span>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': openGroups.newsletter }"></i>
                        </button>
                        <div x-show="openGroups.newsletter" x-collapse class="space-y-0.5">
                            <a href="{{ route('admin.newsletters.campaigns.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.newsletters.campaigns.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-bullhorn sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Campaigns</span>
                            </a>
                            <a href="{{ route('admin.newsletter.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.newsletter.index') || request()->routeIs('admin.newsletter.export') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-users sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Subscribers</span>
                            </a>
                            <a href="{{ route('admin.subscriber-lists.index') }}"
                               class="sidebar-nav-link sidebar-nav-link--child {{ request()->routeIs('admin.subscriber-lists.*') ? 'sidebar-nav-link--active' : '' }}">
                                <i class="fas fa-list-ul sidebar-nav-icon"></i>
                                <span class="sidebar-nav-text">Lists</span>
                            </a>
                        </div>
                    </div>

                    {{-- Newsletter collapsed icon --}}
                    <a href="{{ route('admin.newsletters.campaigns.index') }}"
                       x-show="collapsed"
                       class="sidebar-nav-link sidebar-nav-link--collapsed {{ request()->routeIs('admin.newsletter.*') || request()->routeIs('admin.newsletters.*') || request()->routeIs('admin.subscriber-lists.*') ? 'sidebar-nav-link--active' : '' }}"
                       title="Newsletter">
                        <i class="fas fa-envelope sidebar-nav-icon"></i>
                    </a>

                    <a href="{{ route('admin.email-previews.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.email-previews.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Email Previews' : ''">
                        <i class="fas fa-eye sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Email Previews</span>
                    </a>
                </div>

                {{-- SYSTEM section --}}
                <div class="pt-3" x-show="!collapsed" x-transition.opacity>
                    <span class="sidebar-section-header">System</span>
                </div>
                <div :class="{ 'pt-3': collapsed }">
                    <a href="{{ route('admin.settings.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.settings.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Settings' : ''">
                        <i class="fas fa-cog sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Settings</span>
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="sidebar-nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'sidebar-nav-link--active' : '' }}"
                       :class="{ 'sidebar-nav-link--collapsed': collapsed }"
                       :title="collapsed ? 'Audit Log' : ''">
                        <i class="fas fa-history sidebar-nav-icon"></i>
                        <span class="sidebar-nav-text" x-show="!collapsed" x-transition.opacity>Audit Log</span>
                    </a>
                </div>
            </nav>

            {{-- Sidebar footer: collapse toggle (desktop only) --}}
            <div class="hidden lg:flex items-center justify-center h-12 border-t border-gray-700 flex-shrink-0">
                <button @click="toggleCollapse()" class="p-2 text-gray-400 hover:text-white transition-colors" title="Toggle sidebar">
                    <i class="fas fa-angles-left transition-transform duration-300" :class="{ 'rotate-180': collapsed }"></i>
                </button>
            </div>
        </aside>

        {{-- ============================================================
             MAIN CONTENT AREA (top bar + content)
             ============================================================ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 h-14 flex items-center justify-between px-4 sm:px-6 flex-shrink-0 shadow-sm z-30">
                {{-- Left: hamburger (mobile) + page context --}}
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true"
                            class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 transition-colors"
                            aria-label="Open navigation menu">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <span class="text-sm font-medium text-gray-500 hidden sm:inline">{{ config('business.profile.name') }}</span>
                </div>

                {{-- Right: notification bell + view site + logout --}}
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <x-admin.notification-bell />
                    <a href="{{ route('home') }}" class="btn-admin-secondary btn-admin-sm hidden sm:inline-flex">
                        <i class="fas fa-external-link-alt mr-2"></i>View Site
                    </a>
                    <a href="{{ route('home') }}" class="p-2 text-gray-500 hover:text-gray-700 sm:hidden" title="View Site">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-admin-primary btn-admin-sm hidden sm:inline-flex">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 sm:hidden" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Page Heading --}}
            @if (isset($header))
                <div class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            {{ $header }}
                        </h2>
                    </div>
                </div>
            @endif

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-auto max-w-7xl mt-4 mx-4 sm:mx-6 lg:mx-8" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-auto max-w-7xl mt-4 mx-4 sm:mx-6 lg:mx-8" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
