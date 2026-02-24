<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check for scheduled newsletters every 5 minutes
Schedule::command('newsletters:send-scheduled')->everyFiveMinutes();

// Send review request emails daily at 10 AM
Schedule::command('orders:send-review-requests')->dailyAt('10:00');

// Process paid orders awaiting fulfillment every 15 minutes
Schedule::command('orders:process-fulfillment')->everyFifteenMinutes();

// Send abandoned cart reminder emails hourly
Schedule::command('cart:send-abandoned-emails')->hourly();

// Send welcome emails to new customers every 30 minutes
Schedule::command('customers:send-welcome-emails')->everyThirtyMinutes();

// Send post-purchase follow-up emails daily at 11 AM
Schedule::command('orders:send-post-purchase-follow-ups')->dailyAt('11:00');

// Send win-back emails to inactive customers daily at noon
Schedule::command('customers:send-win-back-emails')->dailyAt('12:00');

// Send back-in-stock notifications every 15 minutes
Schedule::command('notifications:send-back-in-stock')->everyFifteenMinutes();

// Clean up expired GDPR data export files daily
Schedule::command('exports:clean-expired')->daily();

// ─── Events ─────────────────────────────────────────────────────────

// Send event reminder emails daily at 9 AM
Schedule::command('events:send-reminders')->dailyAt('09:00');

// ─── Printful ────────────────────────────────────────────────────────

// Sync Printful product catalog daily at 2 AM
Schedule::command('printful:sync-catalog')->dailyAt('02:00');

// Sync Printful variant pricing daily at 3 AM
Schedule::command('printful:sync-prices')->dailyAt('03:00');

// Poll Printful for order status updates every 2 hours (webhook backup)
Schedule::command('printful:check-orders')->everyTwoHours();
