<x-mail::message>
# Thank You for Your Order!

Hello {{ $customer->name }},

Thank you for your recent order (#{{ $order->id }}) with {{ config('app.name') }}.

We've created an account for you so you can easily track your orders and book appointments in the future.

## Complete Your Account Setup

Click the button below to set your password and activate your account:

<x-mail::button :url="$claimUrl">
Set Up My Account
</x-mail::button>

This link will expire in 7 days.

## Order Summary

**Order #{{ $order->id }}**
**Total:** ${{ number_format($order->total_amount, 2) }}
**Status:** {{ ucfirst($order->payment_status) }}

You can view your order details once you've set up your account.

Thanks,<br>
{{ config('app.name') }}

<x-mail::subcopy>
If you're having trouble clicking the button, copy and paste the URL below into your web browser:
{{ $claimUrl }}
</x-mail::subcopy>
</x-mail::message>
