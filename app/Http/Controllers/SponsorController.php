<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\SponsorTier;

class SponsorController extends Controller
{
    /**
     * Display sponsors grouped by tier.
     */
    public function index()
    {
        $tiers = SponsorTier::ordered()
            ->with(['sponsors' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->get();

        return view('sponsors.index', compact('tiers'));
    }
}
