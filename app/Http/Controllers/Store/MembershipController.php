<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    protected MembershipService $membershipService;

    public function __construct(MembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
    }

    /**
     * Display membership pricing page (public)
     */
    public function index()
    {
        $tiers = MembershipTier::active()->ordered()->get();
        $currentMembership = null;

        if (Auth::check()) {
            $currentMembership = $this->membershipService->getActiveMembership(Auth::user());
        }

        return view('memberships.index', compact('tiers', 'currentMembership'));
    }

    /**
     * Subscribe to a membership tier
     */
    public function subscribe(Request $request, MembershipTier $tier)
    {
        $customer = Auth::user();

        // Check if customer already has this tier
        $existing = $this->membershipService->getActiveMembership($customer);
        if ($existing && $existing->membership_tier_id === $tier->id) {
            return redirect()->route('memberships.manage')
                ->with('info', 'You already have an active membership for this tier.');
        }

        // If Stripe price ID exists, redirect to Stripe Checkout
        if ($tier->stripe_price_id) {
            try {
                $checkoutUrl = $this->membershipService->createCheckoutSession($customer, $tier);
                return redirect($checkoutUrl);
            } catch (\Exception $e) {
                Log::error('Membership checkout failed', [
                    'customer_id' => $customer->id,
                    'tier_id' => $tier->id,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('memberships.index')
                    ->with('error', 'Unable to process payment. Please try again.');
            }
        }

        // Manual activation (no Stripe - for free tiers or manual processing)
        $this->membershipService->activateMembership($customer, $tier, '');
        return redirect()->route('memberships.manage')
            ->with('success', 'Welcome! Your ' . $tier->name . ' membership is now active.');
    }

    /**
     * Handle successful Stripe checkout
     */
    public function success(Request $request)
    {
        $tierId = $request->query('tier');
        $sessionId = $request->query('session_id');

        $tier = MembershipTier::findOrFail($tierId);
        $customer = Auth::user();

        // Try to retrieve subscription ID from Stripe session
        if ($sessionId) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $session = \Stripe\Checkout\Session::retrieve($sessionId);

                if ($session->subscription) {
                    // Check if already activated by webhook
                    $existing = \App\Models\Membership::where('stripe_subscription_id', $session->subscription)->first();
                    if (!$existing) {
                        $this->membershipService->activateMembership($customer, $tier, $session->subscription);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Membership success page: failed to retrieve session', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('memberships.success', compact('tier'));
    }

    /**
     * Manage current membership
     */
    public function manage()
    {
        $customer = Auth::user();
        $membership = $this->membershipService->getActiveMembership($customer);
        $tiers = MembershipTier::active()->ordered()->get();

        return view('memberships.manage', compact('membership', 'tiers'));
    }

    /**
     * Cancel membership subscription
     */
    public function cancel(Request $request)
    {
        $customer = Auth::user();
        $membership = $this->membershipService->getActiveMembership($customer);

        if (!$membership) {
            return redirect()->route('memberships.index')
                ->with('error', 'No active membership found.');
        }

        $success = $this->membershipService->cancelStripeSubscription($membership);

        if ($success) {
            return redirect()->route('memberships.manage')
                ->with('success', 'Your membership has been cancelled. You can continue to use your benefits until the end of your billing period.');
        }

        return redirect()->route('memberships.manage')
            ->with('error', 'Unable to cancel membership. Please contact support.');
    }
}
