<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function index(Request $request)
    {
        $donations = Donation::with('tier')
            ->when($request->input('search'), fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                  ->orWhere('donor_email', 'like', "%{$search}%");
            }))
            ->when($request->input('status'), fn ($q, $status) => $q->where('payment_status', $status))
            ->when($request->input('type'), fn ($q, $type) => $q->where('donation_type', $type))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $stats = $this->donationService->getStats();

        return view('admin.donations.index', compact('donations', 'stats'));
    }

    public function show(Donation $donation)
    {
        $donation->load(['tier', 'customer', 'recurringDonation']);
        return view('admin.donations.show', compact('donation'));
    }
}
