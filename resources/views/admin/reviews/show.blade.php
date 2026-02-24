@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Review Details</h1>
                <p class="text-gray-600 mt-1">View and manage this review</p>
            </div>
            <a href="{{ route('admin.reviews.index') }}"
               class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reviews
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Review Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Review Card -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="flex items-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-2xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-3 text-xl font-bold text-gray-900">{{ $review->rating }}.0</span>
                                    </div>
                                    @if($review->verified_purchase)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-check-circle mr-2"></i> Verified Purchase
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if($review->status === 'approved')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Approved
                                        </span>
                                    @elseif($review->status === 'pending')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i> Pending Review
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Title -->
                            @if($review->title)
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $review->title }}</h2>
                            @endif

                            <!-- Comment -->
                            @if($review->comment)
                                <div class="prose max-w-none text-gray-700 mb-6">
                                    {{ $review->comment }}
                                </div>
                            @endif

                            <!-- Meta Information -->
                            <div class="border-t border-gray-200 pt-4 mt-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        <strong>Customer:</strong> {{ $review->customer->name }}
                                    </div>
                                    <div>
                                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                                        <strong>Email:</strong> {{ $review->customer->email }}
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                        <strong>Submitted:</strong> {{ $review->created_at->format('F d, Y g:i A') }}
                                    </div>
                                    @if($review->helpful_count > 0 || $review->not_helpful_count > 0)
                                        <div>
                                            <i class="fas fa-thumbs-up mr-2 text-gray-400"></i>
                                            <strong>Helpful:</strong> {{ $review->helpful_count }} / {{ $review->helpful_count + $review->not_helpful_count }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Moderation Actions -->
                        @if($review->status === 'pending')
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <div class="flex gap-3">
                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded transition-colors duration-200">
                                            <i class="fas fa-check mr-2"></i>Approve Review
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded transition-colors duration-200">
                                            <i class="fas fa-times mr-2"></i>Reject Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Admin Response Section -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">
                                <i class="fas fa-reply mr-2 text-abs-primary"></i>Admin Response
                            </h3>

                            @if($review->admin_response)
                                <div class="bg-purple-50 border-l-4 border-purple-500 p-4 mb-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-user-shield text-purple-500 text-2xl"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-purple-800 mb-1">
                                                {{ config('business.profile.name', config('app.name')) }}
                                            </p>
                                            <p class="text-sm text-gray-700">
                                                {{ $review->admin_response }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Responded {{ $review->responded_at->format('F d, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-4">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    To update this response, submit a new one below.
                                </p>
                            @else
                                <p class="text-gray-600 mb-4">
                                    Add a public response to this review that will be displayed alongside it on your website.
                                </p>
                            @endif

                            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                                        Your Response
                                    </label>
                                    <textarea name="admin_response"
                                              id="admin_response"
                                              rows="4"
                                              required
                                              placeholder="Thank you for your review..."
                                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">{{ old('admin_response') }}</textarea>
                                    @error('admin_response')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Maximum 1000 characters. This will be publicly visible.
                                    </p>
                                </div>

                                <button type="submit"
                                        class="btn-admin-primary">
                                    <i class="fas fa-paper-plane mr-2"></i>{{ $review->admin_response ? 'Update Response' : 'Submit Response' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Reviewed Item -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Reviewed Item</h3>
                            <div class="flex items-start space-x-4">
                                @if($review->reviewable && $review->reviewable->images && count($review->reviewable->images) > 0)
                                    <img src="{{ asset('storage/' . $review->reviewable->images[0]) }}"
                                         alt="{{ $review->reviewable->name }}"
                                         class="w-20 h-20 object-cover rounded">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">
                                        {{ class_basename($review->reviewable_type) }}
                                    </p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $review->reviewable->name ?? 'N/A' }}
                                    </p>
                                    @if($review->reviewable && $review->reviewable_type === 'App\Models\Product')
                                        <a href="{{ route('products.show', $review->reviewable->slug) }}"
                                           class="text-sm text-abs-primary hover:underline mt-2 inline-block"
                                           target="_blank">
                                            View Product <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Stats</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Review ID</span>
                                    <span class="font-semibold text-gray-900">#{{ $review->id }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span class="font-semibold text-gray-900 capitalize">{{ $review->status }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Created</span>
                                    <span class="font-semibold text-gray-900">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if($review->updated_at != $review->created_at)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Last Updated</span>
                                        <span class="font-semibold text-gray-900">{{ $review->updated_at->diffForHumans() }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Reviews -->
                    @if($relatedReviews->count() > 0)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">
                                    Other Reviews for This Item
                                </h3>
                                <div class="space-y-3">
                                    @foreach($relatedReviews as $relatedReview)
                                        <a href="{{ route('admin.reviews.show', $relatedReview) }}"
                                           class="block p-3 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-xs {{ $i <= $relatedReview->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-2 text-xs text-gray-600">{{ $relatedReview->rating }}.0</span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $relatedReview->customer->name }}</p>
                                            @if($relatedReview->title)
                                                <p class="text-xs text-gray-600 mt-1">{{ Str::limit($relatedReview->title, 50) }}</p>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Danger Zone -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-red-200">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-red-900 mb-4">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Permanently delete this review. This action cannot be undone.
                            </p>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to permanently delete this review? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>Delete Review
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
