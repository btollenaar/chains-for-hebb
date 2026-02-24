@component('mail::message')
# Thank You for Your Generous Donation!

Dear {{ $donation->donor_name }},

Your donation of **${{ number_format($donation->amount, 2) }}** to **Chains for Hebb** has been received.

@if($donation->tier)
**Donation Tier:** {{ $donation->tier->name }}

@if($donation->tier->perks)
**Your Perks:**
{{ $donation->tier->perks }}
@endif
@endif

@if($donation->tax_receipt_number)
**Tax Receipt Number:** {{ $donation->tax_receipt_number }}

Please save this for your tax records. Chains for Hebb is working to build an 18-hole disc golf course at Hebb County Park in West Linn, Oregon.
@endif

Every dollar brings us closer to building an amazing disc golf course for our community.

@component('mail::button', ['url' => route('progress.index')])
See Our Progress
@endcomponent

Thank you for supporting Chains for Hebb!

{{ config('app.name') }}
@endcomponent
