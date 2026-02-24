<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorTier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SponsorTierController extends Controller
{
    public function index()
    {
        $tiers = SponsorTier::ordered()->withCount('sponsors')->get();
        return view('admin.sponsor-tiers.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.sponsor-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'min_amount' => 'required|numeric|min:0',
            'perks' => 'nullable|string|max:2000',
            'logo_size' => 'nullable|in:xl,lg,md,sm',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        SponsorTier::create($validated);

        return redirect()->route('admin.sponsor-tiers.index')->with('success', 'Sponsor tier created.');
    }

    public function edit(SponsorTier $sponsorTier)
    {
        return view('admin.sponsor-tiers.edit', compact('sponsorTier'));
    }

    public function update(Request $request, SponsorTier $sponsorTier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'min_amount' => 'required|numeric|min:0',
            'perks' => 'nullable|string|max:2000',
            'logo_size' => 'nullable|in:xl,lg,md,sm',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $sponsorTier->update($validated);

        return redirect()->route('admin.sponsor-tiers.index')->with('success', 'Sponsor tier updated.');
    }

    public function destroy(SponsorTier $sponsorTier)
    {
        $sponsorTier->delete();
        return redirect()->route('admin.sponsor-tiers.index')->with('success', 'Sponsor tier deleted.');
    }
}
