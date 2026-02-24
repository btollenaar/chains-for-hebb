<x-mail::message>
@if($returnRequest->status === 'approved')
# Return Request Approved

Your return request **#{{ $returnRequest->return_number }}** for order **#{{ $returnRequest->order->order_number }}** has been approved.

**Refund Amount:** ${{ number_format($returnRequest->refund_amount, 2) }}
**Refund Method:** {{ ucfirst(str_replace('_', ' ', $returnRequest->refund_method)) }}

@if($returnRequest->admin_notes)
**Note from our team:** {{ $returnRequest->admin_notes }}
@endif

@if($returnRequest->refund_method === 'original')
Your refund will be processed to your original payment method. Please allow 5-10 business days for the refund to appear.
@elseif($returnRequest->refund_method === 'store_credit')
Store credit has been added to your account and can be used on your next purchase.
@else
Our team will process your refund manually. You will be notified once it has been completed.
@endif

@elseif($returnRequest->status === 'rejected')
# Return Request Update

We've reviewed your return request **#{{ $returnRequest->return_number }}** for order **#{{ $returnRequest->order->order_number }}**.

Unfortunately, we are unable to approve this return at this time.

@if($returnRequest->admin_notes)
**Reason:** {{ $returnRequest->admin_notes }}
@endif

If you believe this is an error or have questions, please don't hesitate to contact us.

@elseif($returnRequest->status === 'completed')
# Refund Processed

Great news! The refund for your return request **#{{ $returnRequest->return_number }}** has been processed.

**Refund Amount:** ${{ number_format($returnRequest->refund_amount, 2) }}
**Refund Method:** {{ ucfirst(str_replace('_', ' ', $returnRequest->refund_method)) }}

@if($returnRequest->refund_method === 'original')
Please allow 5-10 business days for the refund to appear on your statement.
@endif
@endif

<x-mail::button :url="route('returns.show', $returnRequest)">
View Return Details
</x-mail::button>

Thank you for your patience.

{{ config('app.name') }}
</x-mail::message>
