<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $newsletter->subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #2E2A25;
            padding: 20px;
            text-align: center;
        }
        .email-header img {
            max-width: 200px;
            height: auto;
        }
        .email-body {
            padding: 30px 20px;
        }
        .email-footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666666;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            margin: 10px 0;
        }
        .email-footer a {
            color: #2E2A25;
            text-decoration: underline;
        }
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-body {
                padding: 20px 15px !important;
            }
        }
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a;
            }
            .email-container {
                background-color: #2d2d2d;
            }
            .email-body {
                color: #e0e0e0;
            }
        }
        /* User content styles */
        .newsletter-content h1,
        .newsletter-content h2,
        .newsletter-content h3 {
            color: #2E2A25;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .newsletter-content p {
            margin-bottom: 15px;
        }
        .newsletter-content a {
            color: #D77F48;
            text-decoration: none;
        }
        .newsletter-content a:hover {
            text-decoration: underline;
        }
        .newsletter-content img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="email-header">
            @php
                $logo = \App\Models\Setting::get('site_logo');
            @endphp
            @if($logo)
                <img src="{{ asset($logo) }}" alt="{{ config('business.name') }}" />
            @else
                <h1 style="color: #ffffff; margin: 0;">{{ config('business.name') }}</h1>
            @endif
        </div>

        <!-- Newsletter Content -->
        <div class="email-body">
            <div class="newsletter-content">
                {!! $newsletter->content !!}
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{{ config('business.name') }}</strong></p>
            <p>
                {{ config('business.address.street') }}<br>
                {{ config('business.address.city') }}, {{ config('business.address.state') }} {{ config('business.address.zip') }}
            </p>
            <p>
                <a href="mailto:{{ config('business.contact.email') }}">{{ config('business.contact.email') }}</a>
                @if(config('business.contact.phone'))
                    | {{ config('business.contact.phone') }}
                @endif
            </p>
            @if($send)
                <p style="margin-top: 15px;">
                    <a href="{{ route('newsletter.unsubscribe', ['token' => $send->tracking_token]) }}">Unsubscribe from this list</a>
                </p>
            @endif
            <p style="color: #999999; font-size: 12px; margin-top: 15px;">
                You are receiving this email because you subscribed to our newsletter.
            </p>
        </div>
    </div>

    <!-- Tracking Pixel (only for real sends, not tests) -->
    @if($send && !$isTest)
        <img src="{{ route('newsletter.track.open', ['token' => $send->tracking_token]) }}" width="1" height="1" alt="" style="display: block;" />
    @endif
</body>
</html>
