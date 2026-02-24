<x-app-layout>
    @section('title', 'Checkout Cancelled')
    @section('meta_description', 'Your checkout has been cancelled')

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-times-circle text-gray-400 text-5xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Checkout Cancelled</h1>
                <p class="text-xl text-gray-600">Your order has not been placed</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <p class="text-gray-700 mb-6">
                    Your cart items have been saved. You can return to your cart to complete your purchase anytime.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('cart.index') }}" class="btn-primary">
                        Return to Cart
                    </a>
                    <a href="{{ route('home') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-8 rounded-lg transition-colors duration-200">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
