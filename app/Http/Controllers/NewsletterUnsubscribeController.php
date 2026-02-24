<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterUnsubscribeController extends Controller
{
    /**
     * Show the unsubscribe confirmation page.
     */
    public function show(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return view('newsletter.unsubscribe-error', [
                'message' => 'Invalid unsubscribe link. Please check your email and try again.'
            ]);
        }

        // Find the newsletter send by tracking token
        $send = NewsletterSend::where('tracking_token', $token)->first();

        if (!$send) {
            return view('newsletter.unsubscribe-error', [
                'message' => 'This unsubscribe link is invalid or has expired.'
            ]);
        }

        // Load subscription
        $send->load('subscription');

        // Check if already unsubscribed
        if (!$send->subscription->is_active) {
            return view('newsletter.already-unsubscribed', [
                'email' => $send->subscription->email
            ]);
        }

        return view('newsletter.unsubscribe', [
            'token' => $token,
            'email' => $send->subscription->email,
            'subscription' => $send->subscription
        ]);
    }

    /**
     * Process the unsubscribe request.
     */
    public function unsubscribe(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return redirect()->route('newsletter.unsubscribe', ['token' => ''])
                ->with('error', 'Invalid unsubscribe request.');
        }

        // Find the newsletter send by tracking token
        $send = NewsletterSend::where('tracking_token', $token)->first();

        if (!$send) {
            return view('newsletter.unsubscribe-error', [
                'message' => 'This unsubscribe link is invalid or has expired.'
            ]);
        }

        // Load subscription
        $send->load('subscription');
        $subscription = $send->subscription;

        // Unsubscribe the user
        $subscription->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        // Remove from all lists
        $subscription->lists()->detach();

        Log::info('Newsletter unsubscribe', [
            'subscription_id' => $subscription->id,
            'email' => $subscription->email,
            'source' => $subscription->source,
        ]);

        return view('newsletter.unsubscribed', [
            'email' => $subscription->email
        ]);
    }
}
