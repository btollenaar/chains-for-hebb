<x-app-layout>
    @section('title', 'Create Account')
    @section('meta_description', 'Create your account to book appointments and manage orders.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Get Started</p>
                <h1 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Create Account</h1>
                <p class="text-sm mt-2" style="color: var(--on-surface-muted);">Join us to book appointments and manage orders</p>
            </div>

            <div class="card-glass rounded-2xl p-8" data-animate="fade-up">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email Address --}}
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <a class="text-sm text-earth-primary hover:opacity-80 font-medium transition-opacity" href="{{ route('login') }}">
                            Already registered?
                        </a>

                        <button type="submit" class="btn-gradient">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
