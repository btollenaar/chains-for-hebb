@php
    $logoPath = \App\Models\Setting::get('branding.logo_path');
@endphp
@if($logoPath)
    <img src="{{ $logoPath }}" alt="{{ \App\Models\Setting::get('branding.logo_alt', 'PrintStore') }}" {{ $attributes->merge(['class' => 'h-20']) }}>
@else
    <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}" alt="{{ \App\Models\Setting::get('branding.logo_alt', 'PrintStore') }}" {{ $attributes->merge(['class' => 'h-20']) }}>
@endif
