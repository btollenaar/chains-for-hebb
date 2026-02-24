<x-app-layout>
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto">
            <div class="bg-white shadow-md rounded-lg p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Complete Your Account Setup</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Welcome {{ $customer->name }}! Set a password to activate your account and access your order history.
                    </p>
                </div>

                <form method="POST" action="{{ route('account.claim.store', ['customer' => $customer->id]) }}">
                    @csrf

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                            Activate My Account
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-xs text-gray-500 text-center">
                        Your account email: {{ $customer->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
