<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationTier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonationTierController extends Controller
{
    public function index()
    {
        $tiers = DonationTier::ordered()->withCount('donations')->get();
        return view('admin.donation-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.donation-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'suggested_amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:1000',
            'perks' => 'nullable|string|max:2000',
            'badge_icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        DonationTier::create($validated);

        return redirect()->route('admin.donation-tiers.index')->with('success', 'Donation tier created.');
    }

    public function edit(DonationTier $donationTier)
    {
        return view('admin.donation-tiers.edit', compact('donationTier'));
    }

    public function update(Request $request, DonationTier $donationTier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'suggested_amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:1000',
            'perks' => 'nullable|string|max:2000',
            'badge_icon' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $donationTier->update($validated);

        return redirect()->route('admin.donation-tiers.index')->with('success', 'Donation tier updated.');
    }

    public function destroy(DonationTier $donationTier)
    {
        $donationTier->delete();
        return redirect()->route('admin.donation-tiers.index')->with('success', 'Donation tier deleted.');
    }
}
