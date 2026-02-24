<x-app-layout>
    @section('title', 'Log In')
    @section('meta_description', 'Access your account to manage appointments and orders.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Welcome Back</p>
                <h1 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Log In</h1>
            </div>

            <div class="card-glass rounded-2xl p-8" data-animate="fade-up">
                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email Address --}}
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember Me --}}
                    <div class="mb-5">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="rounded border-gray-300 text-earth-primary shadow-sm focus:ring-earth-primary">
                            <span class="ms-2 text-sm" style="color: var(--on-surface-muted);">Remember me</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-earth-primary hover:opacity-80 font-medium transition-opacity" href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif

                        <button type="submit" class="btn-gradient">Log in</button>
                    </div>
                </form>

                {{-- Register Link --}}
                @if (Route::has('register'))
                    <div class="mt-6 pt-6 text-center" style="border-top: 1px solid var(--glass-border);">
                        <p style="color: var(--on-surface-muted);">
                            New user?
                            <a href="{{ route('register') }}" class="text-earth-primary hover:opacity-80 font-semibold transition-opacity">
                                Create an account
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
