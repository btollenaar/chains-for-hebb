<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorTier;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index()
    {
        $sponsors = Sponsor::with('sponsorTier')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.sponsors.index', compact('sponsors'));
    }

    public function create()
    {
        $tiers = SponsorTier::ordered()->get();
        return view('admin.sponsors.create', compact('tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sponsor_tier_id' => 'nullable|exists:sponsor_tiers,id',
            'logo' => 'nullable|image|max:2048',
            'website_url' => 'nullable|url|max:255',
            'sponsorship_amount' => 'required|numeric|min:0',
            'sponsorship_date' => 'nullable|date',
            'sponsorship_expires_at' => 'nullable|date|after:sponsorship_date',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('sponsors', 'public');
        }

        Sponsor::create($validated);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor added.');
    }

    public function edit(Sponsor $sponsor)
    {
        $tiers = SponsorTier::ordered()->get();
        return view('admin.sponsors.edit', compact('sponsor', 'tiers'));
    }

    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sponsor_tier_id' => 'nullable|exists:sponsor_tiers,id',
            'logo' => 'nullable|image|max:2048',
            'website_url' => 'nullable|url|max:255',
            'sponsorship_amount' => 'required|numeric|min:0',
            'sponsorship_date' => 'nullable|date',
            'sponsorship_expires_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('sponsors', 'public');
        }

        $sponsor->update($validated);

        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor updated.');
    }

    public function destroy(Sponsor $sponsor)
    {
        $sponsor->delete();
        return redirect()->route('admin.sponsors.index')->with('success', 'Sponsor deleted.');
    }
}
