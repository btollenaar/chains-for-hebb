<x-mail::message>
# Your Data Export is Ready

Your personal data export has been generated and is ready for download. This file contains all the data we have on file for your account.

<x-mail::button :url="route('data-export.download', ['export' => $dataExport->id, 'signature' => hash_hmac('sha256', $dataExport->id, config('app.key'))])">
Download My Data
</x-mail::button>

**Important:** This download link will expire on {{ $dataExport->expires_at->format('F j, Y') }}.

The export includes:
- Your profile information
- Order history
- Appointment records
- Reviews
- Wishlist items
- Newsletter subscriptions

If you have any questions, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
