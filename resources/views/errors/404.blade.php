<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
    (function(){
        var s = localStorage.getItem('theme');
        if (s === 'dark' || (!s && window.matchMedia('(prefers-color-scheme: dark)').matches))
            document.documentElement.classList.add('dark');
    })();
    </script>
</head>
<body class="font-sans antialiased" style="background-color: var(--surface); color: var(--on-surface);">

    {{-- Gradient Mesh Background --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 rounded-full opacity-20 blur-3xl"
             style="background: radial-gradient(circle, #FF3366 0%, transparent 70%);"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 rounded-full opacity-20 blur-3xl"
             style="background: radial-gradient(circle, #374151 0%, transparent 70%);"></div>
        <div class="absolute top-1/3 right-1/4 w-1/3 h-1/3 rounded-full opacity-10 blur-3xl"
             style="background: radial-gradient(circle, #FF6B8A 0%, transparent 70%);"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-lg mx-auto">
            {{-- 404 Display --}}
            <div class="mb-8">
                <h1 class="text-[8rem] md:text-[12rem] font-display font-bold leading-none text-gradient select-none">
                    404
                </h1>
            </div>

            {{-- Message --}}
            <div class="card-glass rounded-2xl p-8 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-earth-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                    </svg>
                </div>

                <h2 class="text-xl font-display font-bold mb-3" style="color: var(--on-surface);">Page Not Found</h2>
                <p class="mb-6" style="color: var(--on-surface-muted);">
                    The page you're looking for doesn't exist or has been moved.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="/" class="btn-gradient">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Go Home
                    </a>
                    <button onclick="history.back()" class="btn-glass" style="color: var(--on-surface);">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
