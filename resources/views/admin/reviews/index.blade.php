@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Reviews</h1>
        <p class="text-gray-600 mt-1">Manage customer reviews and ratings</p>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards: Mobile-optimized (2 cols mobile, 3 cols tablet, 5 cols desktop) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
                <a href="{{ route('admin.reviews.index') }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Total Reviews</p>
                            <p class="text-2xl md:text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-star text-blue-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl md:text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['pending']) }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-clock text-yellow-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Approved</p>
                            <p class="text-2xl md:text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['approved']) }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" class="bg-white rounded-lg shadow-md p-4 md:p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Rejected</p>
                            <p class="text-2xl md:text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['rejected']) }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </a>

                <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs md:text-sm font-medium text-gray-600">Avg Rating</p>
                            <p class="text-2xl md:text-3xl font-bold text-abs-primary mt-2">{{ $stats['average_rating'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-star text-yellow-500"></i> out of 5
                            </p>
                        </div>
                        <div class="bg-teal-100 rounded-full p-2 md:p-3">
                            <i class="fas fa-chart-line text-abs-primary text-xl md:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions - Desktop Only -->
            <div class="hidden md:block bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Customer, title, or comment..."
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                    id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <!-- Rating Filter -->
                        <div>
                            <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                            <select name="rating"
                                    id="rating"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All Ratings</option>
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type"
                                    id="type"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All Types</option>
                                <option value="products" {{ request('type') === 'products' ? 'selected' : '' }}>Products</option>
                            </select>
                        </div>

                        <!-- Verified Filter -->
                        <div>
                            <label for="verified" class="block text-sm font-medium text-gray-700 mb-1">Verified</label>
                            <select name="verified"
                                    id="verified"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary">
                                <option value="">All</option>
                                <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified Purchase</option>
                                <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="md:col-span-5 flex gap-2">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-filter mr-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.reviews.index') }}"
                               class="btn-admin-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mobile Filter Modal -->
            <x-admin.mobile-filter-modal formAction="{{ route('admin.reviews.index') }}">
                <!-- Search -->
                <div>
                    <label for="mobile-search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" id="mobile-search" value="{{ request('search') }}"
                           placeholder="Customer, title, or comment..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="mobile-status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="mobile-status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label for="mobile-rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" id="mobile-rating"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="mobile-type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="mobile-type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All Types</option>
                        <option value="products" {{ request('type') === 'products' ? 'selected' : '' }}>Products</option>
                        <option value="services" {{ request('type') === 'services' ? 'selected' : '' }}>Services</option>
                    </select>
                </div>

                <!-- Verified Filter -->
                <div>
                    <label for="mobile-verified" class="block text-sm font-medium text-gray-700 mb-2">Verified</label>
                    <select name="verified" id="mobile-verified"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-admin-teal focus:border-admin-teal">
                        <option value="">All</option>
                        <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified Purchase</option>
                        <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                    </select>
                </div>
            </x-admin.mobile-filter-modal>

            <!-- Reviews List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($reviews->count() > 0)
                    <!-- Mobile Cards View - Visible only on mobile -->
                    <div class="grid grid-cols-1 gap-4 md:hidden p-6">
                        @foreach($reviews as $review)
                            <x-admin.table-card
                                :item="$review"
                                route="admin.reviews.show"
                                :fields="[
                                    [
                                        'label' => 'Customer & Review',
                                        'render' => function($item) {
                                            $html = '<div class=\'font-medium text-gray-900\'>' . e($item->customer->name) . '</div>';
                                            if ($item->title) {
                                                $html .= '<div class=\'text-sm font-semibold text-gray-700 mt-1\'>' . e(Str::limit($item->title, 50)) . '</div>';
                                            }
                                            if ($item->comment) {
                                                $html .= '<div class=\'text-sm text-gray-500 mt-1\'>' . e(Str::limit($item->comment, 80)) . '</div>';
                                            }
                                            if ($item->verified_purchase) {
                                                $html .= '<span class=\'inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800\'><i class=\'fas fa-check-circle mr-1\'></i> Verified</span>';
                                            }
                                            return $html;
                                        }
                                    ],
                                    [
                                        'label' => 'Rating',
                                        'render' => function($item) {
                                            $stars = '';
                                            for ($i = 1; $i <= 5; $i++) {
                                                $stars .= '<i class=\'fas fa-star ' . ($i <= $item->rating ? 'text-yellow-400' : 'text-gray-300') . '\'></i>';
                                            }
                                            return '<div class=\'flex items-center\'>' . $stars . '<span class=\'ml-2 text-sm text-gray-600\'>(' . $item->rating . ')</span></div>';
                                        }
                                    ],
                                    [
                                        'label' => 'Item',
                                        'render' => function($item) {
                                            return '<div class=\'text-sm text-gray-900\'>' . e(class_basename($item->reviewable_type)) . '</div>' .
                                                   '<div class=\'text-sm text-gray-500\'>' . e($item->reviewable->name ?? 'N/A') . '</div>';
                                        }
                                    ],
                                    [
                                        'label' => 'Status',
                                        'render' => function($item) {
                                            $statusBadges = [
                                                'approved' => '<span class=\'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800\'><i class=\'fas fa-check-circle mr-1\'></i> Approved</span>',
                                                'pending' => '<span class=\'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800\'><i class=\'fas fa-clock mr-1\'></i> Pending</span>',
                                                'rejected' => '<span class=\'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800\'><i class=\'fas fa-times-circle mr-1\'></i> Rejected</span>',
                                            ];
                                            return $statusBadges[$item->status] ?? '';
                                        }
                                    ]
                                ]"
                                :actions="[
                                    ['route' => 'admin.reviews.show', 'icon' => 'fa-eye', 'color' => 'blue', 'label' => 'View review']
                                ]"
                            />
                        @endforeach
                    </div>

                    <!-- Desktop Table - Hidden on mobile -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Review
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rating
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reviews as $review)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $review->customer->name }}
                                                </div>
                                                @if($review->title)
                                                    <div class="text-sm font-semibold text-gray-700 mt-1">
                                                        {{ Str::limit($review->title, 50) }}
                                                    </div>
                                                @endif
                                                @if($review->comment)
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        {{ Str::limit($review->comment, 100) }}
                                                    </div>
                                                @endif
                                                @if($review->verified_purchase)
                                                    <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-check-circle mr-1"></i> Verified Purchase
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ class_basename($review->reviewable_type) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $review->reviewable->name ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-600">({{ $review->rating }})</span>
                                            </div>
                                            @if($review->helpful_count > 0)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ $review->helpful_count }} found helpful
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($review->status === 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Approved
                                                </span>
                                            @elseif($review->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i> Pending
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Rejected
                                                </span>
                                            @endif
                                            @if($review->admin_response)
                                                <div class="mt-1">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        <i class="fas fa-reply mr-1"></i> Responded
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $review->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.reviews.show', $review) }}"
                                               aria-label="View review details"
                                               class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </a>

                                            @if($review->status === 'pending')
                                                <form action="{{ route('admin.reviews.approve', $review) }}"
                                                      method="POST"
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            aria-label="Approve review"
                                                            class="text-green-600 hover:text-green-900 mr-3">
                                                        <i class="fas fa-check" aria-hidden="true"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.reviews.reject', $review) }}"
                                                      method="POST"
                                                      class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            aria-label="Reject review"
                                                            class="text-red-600 hover:text-red-900 mr-3">
                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.reviews.destroy', $review) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('Permanently delete this review? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        aria-label="Delete review"
                                                        class="link-admin-danger">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $reviews->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-star text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews found</h3>
                        <p class="text-gray-500">
                            @if(request()->hasAny(['search', 'status', 'rating', 'type', 'verified']))
                                No reviews match your current filters. Try adjusting your search criteria.
                            @else
                                Customer reviews will appear here once submitted.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
