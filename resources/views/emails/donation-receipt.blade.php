@component('mail::message')
# Donation Receipt

**Receipt Number:** {{ $donation->tax_receipt_number }}
**Date:** {{ $donation->created_at->format('F j, Y') }}
**Amount:** ${{ number_format($donation->amount, 2) }}
**Donor:** {{ $donation->donor_name }}
**Email:** {{ $donation->donor_email }}

@if($donation->donation_type === 'recurring')
**Type:** Recurring Donation
@endif

---

**Organization:** {{ config('business.fundraising.organization_name') }}
**Purpose:** Building an 18-hole disc golf course at Hebb County Park, West Linn, OR

Please retain this receipt for your tax records.

{{ config('app.name') }}
@endcomponent
