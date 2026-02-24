{{ $newsletter->subject }}

{{ str_repeat('=', strlen($newsletter->subject)) }}

{{ $newsletter->plain_text_content ?: $newsletter->generatePlainText() }}

---

{{ config('business.name') }}
{{ config('business.address.street') }}
{{ config('business.address.city') }}, {{ config('business.address.state') }} {{ config('business.address.zip') }}

@if($send)
Unsubscribe: {{ route('newsletter.unsubscribe', ['token' => $send->tracking_token]) }}
@endif
