<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', config('business.profile.tagline'))</title>
        <meta name="description" content="@yield('meta_description', config('business.profile.tagline'))">
        <link rel="canonical" href="@yield('canonical_url', url()->current())">

        {{-- Open Graph --}}
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="@yield('og_title', config('app.name') . ' - ' . View::yieldContent('title', config('business.profile.tagline')))">
        <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', config('business.profile.tagline')))">
        <meta property="og:url" content="@yield('canonical_url', url()->current())">
        <meta property="og:type" content="@yield('og_type', 'website')">
        @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
        @endif

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('og_title', config('app.name') . ' - ' . View::yieldContent('title', config('business.profile.tagline')))">
        <meta name="twitter:description" content="@yield('og_description', View::yieldContent('meta_description', config('business.profile.tagline')))">
        @hasSection('og_image')
        <meta name="twitter:image" content="@yield('og_image')">
        @endif

        @stack('head')

        <link rel="shortcut icon" href="{{ \App\Models\Setting::get('branding.favicon_path') ?: asset('favicon.ico') }}" type="image/x-icon">

        <!-- Dark mode: prevent flash of wrong theme -->
        <script>
        (function(){
            var s = localStorage.getItem('theme');
            if (s === 'dark' || (!s && window.matchMedia('(prefers-color-scheme: dark)').matches))
                document.documentElement.classList.add('dark');
        })();
        </script>

        <!-- Google Fonts: Inter + Oswald + Playfair Display -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Oswald:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

        <!-- Font Awesome CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
              integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
              crossorigin="anonymous"
              referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notification.js', 'resources/js/cart.js'])

        {{-- Google Analytics 4 --}}
        @if(config('services.google.analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
        @endif

        {{-- Meta/Facebook Pixel --}}
        @if(config('services.meta.pixel_id'))
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ config('services.meta.pixel_id') }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ config('services.meta.pixel_id') }}&ev=PageView&noscript=1"/></noscript>
        @endif

        <!-- Dynamic Theme Colors -->
        <style>
            :root {
                --color-primary: {{ \App\Models\Setting::get('theme.primary_color', '#2E2A25') }};
                --color-secondary: {{ \App\Models\Setting::get('theme.secondary_color', '#C9B79C') }};
                --color-accent: {{ \App\Models\Setting::get('theme.accent_color', '#D77F48') }};
                --color-admin: {{ \App\Models\Setting::get('theme.admin_color', '#2D6069') }};
                --color-background: {{ \App\Models\Setting::get('theme.background_color', '#F2ECE4') }};
            }

            /* Apply theme colors to key elements */
            .btn-primary {
                background-color: var(--color-accent);
            }

            .btn-primary:hover {
                background-color: color-mix(in srgb, var(--color-accent) 85%, black);
            }

            .text-abs-primary {
                color: var(--color-primary);
            }

            .bg-abs-primary {
                background-color: var(--color-primary);
            }

            .text-brand-color {
                color: var(--color-secondary);
            }

            .bg-brand-color {
                background-color: var(--color-secondary);
            }

            .text-accent-color {
                color: var(--color-accent);
            }

            .bg-accent-color {
                background-color: var(--color-accent);
            }

            .bg-abs-bg {
                background-color: var(--color-background);
            }

            .border-abs-primary {
                border-color: var(--color-primary);
            }

            .border-accent-color {
                border-color: var(--color-accent);
            }
        </style>
    </head>
    <body class="font-sans antialiased transition-colors duration-300" style="background-color: var(--surface); color: var(--on-surface);">
        <div class="min-h-screen w-full" style="background-color: var(--surface);">
            <!-- Header -->
            <x-header />

            <!-- Global Notification Component -->
            <x-notification />

            <!-- Hidden flash message data for JavaScript -->
            @if (session('success'))
                <div data-flash-success="{{ session('success') }}" style="display: none;"></div>
            @endif
            @if (session('error'))
                <div data-flash-error="{{ session('error') }}" style="display: none;"></div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main style="padding-top: 72px;">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <x-footer />

            <!-- Email Capture Popup -->
            <x-email-popup />

            <!-- Cookie Consent Banner -->
            <x-cookie-consent />
        </div>

        @stack('scripts')
    </body>
</html>
