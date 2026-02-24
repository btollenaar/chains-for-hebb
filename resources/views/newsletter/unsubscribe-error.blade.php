<x-app-layout>
    @section('title', 'Unsubscribe Error')

<div class="min-h-screen bg-abs-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-abs-primary mb-2">Unsubscribe Error</h2>
            <p class="text-gray-600">{{ $message }}</p>
        </div>

        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        The unsubscribe link may be invalid, expired, or has already been used. Please check your email for the most recent newsletter and try again.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-3 mb-6">
            <h3 class="font-semibold text-abs-primary">What you can do:</h3>
            <ul class="list-disc list-inside text-sm text-gray-700 space-y-2">
                <li>Check your email for the most recent newsletter with a valid unsubscribe link</li>
                <li>Contact us directly to be removed from the mailing list</li>
                @auth
                    <li>Manage your email preferences in your account settings</li>
                @endauth
            </ul>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="btn-primary w-full text-center block">
                <i class="fas fa-home mr-2"></i>
                Return to Home
            </a>

            <a href="mailto:{{ config('business.contact.email') }}" class="btn-secondary w-full text-center block">
                <i class="fas fa-envelope mr-2"></i>
                Contact Support
            </a>

            @auth
                <a href="{{ route('dashboard') }}" class="btn-secondary w-full text-center block">
                    <i class="fas fa-user mr-2"></i>
                    Go to My Account
                </a>
            @endauth
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>
                Need help? Contact us at
                <a href="mailto:{{ config('business.contact.email') }}" class="text-accent-color hover:underline">
                    {{ config('business.contact.email') }}
                </a>
                or call {{ config('business.contact.phone') }}
            </p>
        </div>
    </div>
</div>
</x-app-layout>
