<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterSubscribeController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $existing = NewsletterSubscription::where('email', $validated['email'])->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => true,
                    'message' => "You're already subscribed! Use code WELCOME10 for 10% off.",
                    'coupon' => 'WELCOME10',
                ]);
            }

            $existing->update([
                'is_active' => true,
                'unsubscribed_at' => null,
            ]);
        } else {
            $subscription = NewsletterSubscription::create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'source' => 'popup',
                'is_active' => true,
                'subscribed_at' => now(),
            ]);

            $allList = SubscriberList::where('is_default', true)->first();
            if ($allList) {
                $subscription->lists()->attach($allList->id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Welcome! Use code WELCOME10 for 10% off your first order.',
            'coupon' => 'WELCOME10',
        ]);
    }
}
