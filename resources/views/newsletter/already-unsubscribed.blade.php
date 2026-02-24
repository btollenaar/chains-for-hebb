<x-app-layout>
    @section('title', 'Already Unsubscribed')

<div class="min-h-screen bg-abs-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-4">
                <i class="fas fa-info-circle text-2xl text-blue-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-abs-primary mb-2">Already Unsubscribed</h2>
            <p class="text-gray-600">This email address has already been unsubscribed from our newsletter.</p>
        </div>

        <div class="mb-6">
            <p class="text-center text-lg font-semibold text-abs-primary bg-abs-bg p-3 rounded">
                {{ $email }}
            </p>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        You are not receiving our newsletters. If you continue to receive emails from us, please contact our support team.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-green-900 mb-2">
                <i class="fas fa-heart mr-2"></i>
                Would you like to re-subscribe?
            </h3>
            <p class="text-sm text-green-700 mb-3">
                Stay updated with our latest news, special offers, and wellness tips delivered straight to your inbox.
            </p>
            <a href="{{ route('home') }}#newsletter-signup" class="inline-block bg-green-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-envelope mr-2"></i>
                Re-Subscribe to Newsletter
            </a>
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

        <div class="mt-6 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>
                Still receiving emails? Contact us at
                <a href="mailto:{{ config('business.contact.email') }}" class="text-accent-color hover:underline">
                    {{ config('business.contact.email') }}
                </a>
            </p>
        </div>
    </div>
</div>
</x-app-layout>
