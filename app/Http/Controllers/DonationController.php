<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationTier;
use App\Services\DonationService;
use App\Services\FundraisingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected FundraisingService $fundraisingService
    ) {}

    /**
     * Show the donation page with tiers.
     */
    public function index()
    {
        $tiers = DonationTier::active()->ordered()->get();
        $donorWall = $this->donationService->getDonorWall(20);
        $stats = $this->donationService->getStats();
        $progressData = $this->fundraisingService->getProgressData();

        return view('donate.index', compact('tiers', 'donorWall', 'stats', 'progressData'));
    }

    /**
     * Process a donation via Stripe Checkout.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:50000',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'required|email|max:255',
            'tier_id' => 'nullable|exists:donation_tiers,id',
            'is_anonymous' => 'nullable|boolean',
            'donor_message' => 'nullable|string|max:500',
            'display_name' => 'nullable|string|max:255',
            'donation_type' => 'nullable|in:one_time,recurring',
            'interval' => 'nullable|in:monthly,quarterly,yearly',
        ]);

        $validated['customer_id'] = Auth::id();

        if (($validated['donation_type'] ?? 'one_time') === 'recurring') {
            $session = $this->donationService->createRecurringCheckoutSession($validated);
        } else {
            $session = $this->donationService->createCheckoutSession($validated);
        }

        return redirect($session->url);
    }

    /**
     * Donation success page.
     */
    public function success(Request $request, Donation $donation)
    {
        return view('donate.success', compact('donation'));
    }

    /**
     * Public donor wall.
     */
    public function wall()
    {
        $donors = $this->donationService->getDonorWall(100);
        $stats = $this->donationService->getStats();

        return view('donate.wall', compact('donors', 'stats'));
    }
}
