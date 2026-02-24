@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <a href="{{ route('admin.returns.index') }}" class="text-sm text-admin-teal hover:underline mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Returns
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Return #{{ $return->return_number }}</h1>
                <p class="text-gray-600 mt-1">Submitted {{ $return->created_at->format('M j, Y \a\t g:i A') }}</p>
            </div>
            @php
                $statusBadge = [
                    'requested' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                ][$return->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $statusBadge }}">
                {{ ucfirst($return->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Return Details --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Return Details</h2>

                <dl class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <dt class="text-sm font-medium text-gray-500">Reason</dt>
                        <dd class="col-span-2 text-sm text-gray-900">{{ \App\Models\ReturnRequest::reasonOptions()[$return->reason] ?? $return->reason }}</dd>
                    </div>

                    @if($return->details)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Details</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $return->details }}</dd>
                        </div>
                    @endif

                    @if($return->items && count($return->items) > 0)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Items</dt>
                            <dd class="col-span-2">
                                <ul class="space-y-1">
                                    @foreach($return->items as $item)
                                        <li class="text-sm text-gray-900">{{ $item['name'] ?? 'Item' }} x{{ $item['quantity'] ?? 1 }}</li>
                                    @endforeach
                                </ul>
                            </dd>
                        </div>
                    @endif

                    @if($return->refund_amount)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Refund Amount</dt>
                            <dd class="col-span-2 text-sm font-bold text-gray-900">${{ number_format($return->refund_amount, 2) }}</dd>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Refund Method</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</dd>
                        </div>
                    @endif

                    @if($return->admin_notes)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Admin Notes</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $return->admin_notes }}</dd>
                        </div>
                    @endif

                    @if($return->processedBy)
                        <div class="grid grid-cols-3 gap-4">
                            <dt class="text-sm font-medium text-gray-500">Processed By</dt>
                            <dd class="col-span-2 text-sm text-gray-900">{{ $return->processedBy->name }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Action Forms --}}
            @if($return->status === 'requested')
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Process Return</h2>

                    <div x-data="{ action: '' }" class="space-y-4">
                        <div class="flex gap-3">
                            <button @click="action = 'approve'" class="btn-admin-success" :class="{ 'ring-2 ring-green-500 ring-offset-2': action === 'approve' }">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                            <button @click="action = 'reject'" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" :class="{ 'ring-2 ring-red-500 ring-offset-2': action === 'reject' }">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </div>

                        {{-- Approve Form --}}
                        <form x-show="action === 'approve'" x-transition action="{{ route('admin.returns.approve', $return) }}" method="POST" class="p-4 bg-green-50 rounded-lg space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount *</label>
                                    <input type="number" name="refund_amount" step="0.01" min="0" max="{{ $return->order->total_amount }}"
                                           value="{{ $return->order->total_amount }}" required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                                    <p class="text-xs text-gray-500 mt-1">Order total: ${{ number_format($return->order->total_amount, 2) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Refund Method *</label>
                                    <select name="refund_method" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm">
                                        @if($return->order->payment_method === 'stripe' && $return->order->stripe_payment_intent_id)
                                            <option value="original">Original Payment (Stripe - Auto)</option>
                                        @endif
                                        <option value="manual">Manual Refund</option>
                                        <option value="store_credit">Store Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                                <textarea name="admin_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm" placeholder="Optional notes..."></textarea>
                            </div>
                            <button type="submit" class="btn-admin-success">
                                <i class="fas fa-check mr-2"></i>Confirm Approval & Process Refund
                            </button>
                        </form>

                        {{-- Reject Form --}}
                        <form x-show="action === 'reject'" x-transition action="{{ route('admin.returns.reject', $return) }}" method="POST" class="p-4 bg-red-50 rounded-lg space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection *</label>
                                <textarea name="admin_notes" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal text-sm" placeholder="Explain why this return is being rejected..."></textarea>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                <i class="fas fa-times mr-2"></i>Confirm Rejection
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($return->status === 'approved')
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Complete Return</h2>
                    <p class="text-sm text-gray-600 mb-4">Mark this return as completed once the refund has been fully processed.</p>
                    <form action="{{ route('admin.returns.complete', $return) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-admin-primary" onclick="return confirm('Mark this return as completed?')">
                            <i class="fas fa-check-double mr-2"></i>Mark as Completed
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Customer</h3>
                <p class="text-sm text-gray-900 font-medium">{{ $return->customer->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $return->customer->email ?? '' }}</p>
                <a href="{{ route('admin.customers.show', $return->customer) }}" class="text-sm text-admin-teal hover:underline mt-2 inline-block">
                    View Customer
                </a>
            </div>

            {{-- Order Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Order</h3>
                <p class="text-sm font-medium text-gray-900">{{ $return->order->order_number }}</p>
                <p class="text-sm text-gray-600">${{ number_format($return->order->total_amount, 2) }}</p>
                <p class="text-sm text-gray-500">{{ $return->order->created_at->format('M j, Y') }}</p>
                <p class="text-sm text-gray-500">Payment: {{ ucfirst($return->order->payment_method) }}</p>
                <a href="{{ route('admin.orders.show', $return->order) }}" class="text-sm text-admin-teal hover:underline mt-2 inline-block">
                    View Order
                </a>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-gray-400 mt-2"></i>
                        <div>
                            <p class="text-sm text-gray-900">Requested</p>
                            <p class="text-xs text-gray-500">{{ $return->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                    @if($return->approved_at)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-circle text-[6px] text-green-500 mt-2"></i>
                            <div>
                                <p class="text-sm text-gray-900">Approved</p>
                                <p class="text-xs text-gray-500">{{ $return->approved_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($return->rejected_at)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-circle text-[6px] text-red-500 mt-2"></i>
                            <div>
                                <p class="text-sm text-gray-900">Rejected</p>
                                <p class="text-xs text-gray-500">{{ $return->rejected_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($return->completed_at)
                        <div class="flex items-start gap-2">
                            <i class="fas fa-circle text-[6px] text-blue-500 mt-2"></i>
                            <div>
                                <p class="text-sm text-gray-900">Completed</p>
                                <p class="text-xs text-gray-500">{{ $return->completed_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
