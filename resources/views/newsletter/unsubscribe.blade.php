<x-app-layout>
    @section('title', 'Unsubscribe from Newsletter')

<div class="min-h-screen bg-abs-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <i class="fas fa-envelope-open-text text-4xl text-accent-color mb-4"></i>
            <h2 class="text-2xl font-bold text-abs-primary">Unsubscribe from Newsletter</h2>
        </div>

        <div class="mb-6">
            <p class="text-gray-700 mb-4">
                We're sorry to see you go! You are about to unsubscribe the following email address from our newsletter:
            </p>
            <p class="text-center text-lg font-semibold text-abs-primary bg-abs-bg p-3 rounded">
                {{ $email }}
            </p>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        If you unsubscribe, you will no longer receive our newsletters and updates. You can re-subscribe at any time by signing up again.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('newsletter.unsubscribe') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-unlink mr-2"></i>
                    Yes, Unsubscribe Me
                </button>
                <a href="{{ url('/') }}" class="flex-1 btn-secondary text-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>
            </div>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Changed your mind? You can also adjust your email preferences in your account settings.</p>
        </div>
    </div>
</div>
</x-app-layout>
