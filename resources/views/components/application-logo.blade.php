@php
    $logoPath = \App\Models\Setting::get('branding.logo_path');
    $alt = \App\Models\Setting::get('branding.logo_alt', 'Chains for Hebb');
@endphp
@if($logoPath)
    <img src="{{ $logoPath }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'h-10']) }}>
@else
    {{-- Light mode logo (hidden in dark mode) --}}
    <img src="{{ asset('images/logo.svg') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'h-10 dark:hidden']) }}>
    {{-- Dark mode logo (hidden in light mode) --}}
    <img src="{{ asset('images/logo-dark.svg') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'h-10 hidden dark:block']) }}>
@endif
