<x-app-layout>
    @section('title', 'Unsubscribed Successfully')

<div class="min-h-screen bg-abs-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-abs-primary mb-2">You've Been Unsubscribed</h2>
            <p class="text-gray-600">Your email address has been successfully removed from our newsletter list.</p>
        </div>

        <div class="mb-6">
            <p class="text-center text-lg font-semibold text-abs-primary bg-abs-bg p-3 rounded">
                {{ $email }}
            </p>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        You will no longer receive newsletters from us. If this was a mistake, you can re-subscribe at any time.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="btn-primary w-full text-center block">
                <i class="fas fa-home mr-2"></i>
                Return to Home
            </a>

            @auth
                <a href="{{ route('dashboard') }}" class="btn-secondary w-full text-center block">
                    <i class="fas fa-user mr-2"></i>
                    Go to My Account
                </a>
            @endauth
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-3">
                We'd love to hear your feedback. Was there something we could improve?
            </p>
            <a href="mailto:{{ config('business.contact.email') }}" class="text-accent-color hover:underline text-sm font-semibold">
                <i class="fas fa-comment mr-1"></i>
                Send Us Feedback
            </a>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>
                If you continue to receive emails, please contact us at
                <a href="mailto:{{ config('business.contact.email') }}" class="text-accent-color hover:underline">
                    {{ config('business.contact.email') }}
                </a>
            </p>
        </div>
    </div>
</div>
</x-app-layout>
