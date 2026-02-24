<x-app-layout>
    @section('title', 'Confirm Password')
    @section('meta_description', 'Confirm your password to continue to your account.')

    <section class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-6">
                <h1 class="page-heading text-5xl font-bold text-[#2E2A25] mb-6">Confirm Password</h1>
                <p class="text-xl text-gray-600">This secure area requires password confirmation</p>
            </div>

            <div class="bg-white shadow-xl rounded-lg p-8 md:p-10">
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Please confirm your password before continuing.') }}
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button>
                            {{ __('Confirm') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
