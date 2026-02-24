<x-app-layout>
    @section('title', 'Verify Email')
    @section('meta_description', 'Verify your email address to activate your account.')

    <section class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6">
                <h1 class="page-heading text-5xl font-bold text-[#2E2A25] mb-6">Verify Email</h1>
                <p class="text-xl text-gray-600">Check your inbox for the verification link</p>
            </div>

            <div class="bg-white shadow-xl rounded-lg p-8 md:p-10">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Thanks for signing up! Before getting started, verify your email address by clicking the link we just sent. If you didn\'t receive it, we can send another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf

                        <div>
                            <x-primary-button>
                                {{ __('Resend Verification Email') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="text-sm text-abs-primary hover:text-gray-800 font-semibold">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
