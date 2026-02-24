@extends('layouts.admin')

@section('title', $subscriberList->name . ' - Subscribers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.subscriber-lists.index') }}" class="text-gray-600 hover:text-admin-teal mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $subscriberList->name }}</h1>
            @if($subscriberList->is_system)
                <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-cog mr-1"></i>
                    System List
                </span>
            @endif
        </div>

        <div class="ml-9">
            @if($subscriberList->description)
                <p class="text-gray-600 mb-2">{{ $subscriberList->description }}</p>
            @endif
            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                <span>
                    <i class="fas fa-tag mr-1"></i>
                    Slug: <span class="font-mono">{{ $subscriberList->slug }}</span>
                </span>
                <span>
                    <i class="fas fa-users mr-1"></i>
                    {{ number_format($subscriberList->subscribers_count) }} subscriber(s)
                </span>
                <span>
                    <i class="fas fa-calendar mr-1"></i>
                    Created {{ $subscriberList->created_at->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        @if(!$subscriberList->is_system)
            <a href="{{ route('admin.subscriber-lists.edit', $subscriberList) }}" class="btn-admin-primary">
                <i class="fas fa-edit mr-2"></i>
                Edit List Details
            </a>
        @endif
        <a href="{{ route('admin.newsletter.index') }}" class="btn-admin-secondary">
            <i class="fas fa-user-plus mr-2"></i>
            Add Subscribers
        </a>
    </div>

    <!-- Search Filter -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('admin.subscriber-lists.show', $subscriberList) }}">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by name or email..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring focus:ring-admin-teal focus:ring-opacity-50">
                </div>
                <button type="submit" class="btn-admin-primary">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.subscriber-lists.show', $subscriberList) }}" class="btn-admin-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Subscribers Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($subscribers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscriber
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Source
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscribed
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscribers as $subscriber)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 rounded-full">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <div class="ml-4">
                                            @if($subscriber->name)
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $subscriber->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $subscriber->email }}
                                                </div>
                                            @else
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $subscriber->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($subscriber->source === 'checkout')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-shopping-cart mr-1"></i>
                                            Checkout
                                        </span>
                                    @elseif($subscriber->source === 'signup_form')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-clipboard-list mr-1"></i>
                                            Signup Form
                                        </span>
                                    @elseif($subscriber->source === 'manual')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <i class="fas fa-user-plus mr-1"></i>
                                            Manual
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">{{ ucfirst($subscriber->source) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $subscriber->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.subscriber-lists.remove-subscriber', ['subscriberList' => $subscriberList, 'subscriber' => $subscriber]) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Remove this subscriber from the list?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                title="Remove from List">
                                            <i class="fas fa-user-minus mr-1"></i>
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subscribers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg mb-2">
                    @if(request('search'))
                        No subscribers found matching your search.
                    @else
                        No subscribers in this list yet.
                    @endif
                </p>
                @if(!request('search'))
                    <p class="text-gray-400 mb-4">
                        Add subscribers from the Newsletter Subscribers page
                    </p>
                    <a href="{{ route('admin.newsletter.index') }}" class="btn-admin-primary">
                        <i class="fas fa-user-plus mr-2"></i>
                        Go to Subscribers
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 mb-1">Managing Subscribers</h3>
                <p class="text-sm text-blue-700">
                    To add subscribers to this list, go to the <a href="{{ route('admin.newsletter.index') }}" class="underline font-semibold">Newsletter Subscribers</a> page, select subscribers, and use the bulk actions menu to assign them to this list.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
