<x-app-layout>
    @section('title', 'Forgot Password')
    @section('meta_description', 'Request a password reset link for your account.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Account Recovery</p>
                <h1 class="text-fluid-2xl font-display font-bold" style="color: var(--on-surface);">Forgot Password</h1>
                <p class="text-sm mt-2" style="color: var(--on-surface-muted);">We'll email you a link to reset your password</p>
            </div>

            <div class="card-glass rounded-2xl p-8" data-animate="fade-up">
                <p class="text-sm mb-6" style="color: var(--on-surface-muted);">
                    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </p>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email Address --}}
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="btn-gradient">Email Password Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
