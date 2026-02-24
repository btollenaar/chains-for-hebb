@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Donation #{{ $donation->id }}</h1>
            <a href="{{ route('admin.donations.index') }}" class="btn-admin-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Donations
            </a>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Donation Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Donation Details Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Donation Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Donor Name</p>
                                <p class="font-semibold text-gray-900">{{ $donation->donor_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <a href="mailto:{{ $donation->donor_email }}" class="text-admin-teal hover:underline">
                                    {{ $donation->donor_email }}
                                </a>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Amount</p>
                                <p class="text-2xl font-bold text-gray-900">${{ number_format($donation->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Donation Type</p>
                                @if($donation->donation_type === 'recurring')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Recurring
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        One-Time
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tier</p>
                                <p class="font-semibold text-gray-900">{{ $donation->tier->name ?? 'No tier' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Payment Status</p>
                                @php
                                    $statusColors = [
                                        'paid' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$donation->payment_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($donation->payment_status) }}
                                </span>
                            </div>
                            @if($donation->stripe_payment_intent_id)
                                <div>
                                    <p class="text-sm text-gray-500">Stripe Payment Intent</p>
                                    <p class="font-mono text-sm text-gray-700">{{ $donation->stripe_payment_intent_id }}</p>
                                </div>
                            @endif
                            @if($donation->stripe_charge_id)
                                <div>
                                    <p class="text-sm text-gray-500">Stripe Charge ID</p>
                                    <p class="font-mono text-sm text-gray-700">{{ $donation->stripe_charge_id }}</p>
                                </div>
                            @endif
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="font-semibold text-gray-900">{{ $donation->created_at->format('F j, Y g:i A') }}</p>
                            </div>
                        </div>

                        @if($donation->message)
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-1">Donor Message</p>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ $donation->message }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Recurring Info -->
                <div class="lg:col-span-1 space-y-6">
                    @if($donation->donation_type === 'recurring')
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-sync-alt text-admin-teal mr-2"></i>Recurring Info
                            </h3>
                            <div class="space-y-3">
                                @if($donation->stripe_subscription_id)
                                    <div>
                                        <p class="text-sm text-gray-500">Subscription ID</p>
                                        <p class="font-mono text-sm text-gray-700">{{ $donation->stripe_subscription_id }}</p>
                                    </div>
                                @endif
                                @if($donation->recurring_interval)
                                    <div>
                                        <p class="text-sm text-gray-500">Interval</p>
                                        <p class="font-semibold text-gray-900">{{ ucfirst($donation->recurring_interval) }}</p>
                                    </div>
                                @endif
                                @if($donation->recurring_status)
                                    <div>
                                        <p class="text-sm text-gray-500">Recurring Status</p>
                                        @php
                                            $recurringColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'paused' => 'bg-yellow-100 text-yellow-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $recurringColor = $recurringColors[$donation->recurring_status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $recurringColor }}">
                                            {{ ucfirst($donation->recurring_status) }}
                                        </span>
                                    </div>
                                @endif
                                @if($donation->current_period_start)
                                    <div>
                                        <p class="text-sm text-gray-500">Current Period Start</p>
                                        <p class="font-semibold text-gray-900">{{ $donation->current_period_start->format('M j, Y') }}</p>
                                    </div>
                                @endif
                                @if($donation->current_period_end)
                                    <div>
                                        <p class="text-sm text-gray-500">Current Period End</p>
                                        <p class="font-semibold text-gray-900">{{ $donation->current_period_end->format('M j, Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-admin-teal mr-2"></i>Donation Info
                            </h3>
                            <p class="text-sm text-gray-500">This is a one-time donation. No recurring subscription is associated with it.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
