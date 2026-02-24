<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    /**
     * Display membership tiers and member stats
     */
    public function index(Request $request)
    {
        // Stats - single query
        $stats = DB::table('memberships')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired,
                SUM(CASE WHEN status = 'past_due' THEN 1 ELSE 0 END) as past_due
            ")->first();

        $tiers = MembershipTier::withCount(['memberships', 'activeMembers'])
            ->ordered()
            ->get();

        // Members list with filters
        $members = Membership::with(['customer', 'tier'])
            ->when($request->search, function ($q, $search) {
                $q->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->tier, function ($q, $tier) {
                $q->where('membership_tier_id', $tier);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.memberships.index', compact('stats', 'tiers', 'members'));
    }

    /**
     * Show tier creation form
     */
    public function create()
    {
        return view('admin.memberships.create');
    }

    /**
     * Store a new membership tier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'billing_interval' => 'required|in:monthly,yearly',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'features' => 'nullable|string',
            'priority_booking' => 'boolean',
            'free_shipping' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'badge_color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['priority_booking'] = $request->boolean('priority_booking');
        $validated['free_shipping'] = $request->boolean('free_shipping');
        $validated['is_active'] = $request->boolean('is_active', true);

        // Parse features from textarea (one per line)
        if (!empty($validated['features'])) {
            $validated['features'] = array_filter(
                array_map('trim', explode("\n", $validated['features']))
            );
        } else {
            $validated['features'] = [];
        }

        MembershipTier::create($validated);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership tier created successfully.');
    }

    /**
     * Show tier details
     */
    public function show(MembershipTier $membership)
    {
        $membership->loadCount(['memberships', 'activeMembers']);

        $recentMembers = Membership::where('membership_tier_id', $membership->id)
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.memberships.show', [
            'tier' => $membership,
            'recentMembers' => $recentMembers,
        ]);
    }

    /**
     * Show tier edit form
     */
    public function edit(MembershipTier $membership)
    {
        return view('admin.memberships.edit', ['tier' => $membership]);
    }

    /**
     * Update a membership tier
     */
    public function update(Request $request, MembershipTier $membership)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'billing_interval' => 'required|in:monthly,yearly',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'features' => 'nullable|string',
            'priority_booking' => 'boolean',
            'free_shipping' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'stripe_price_id' => 'nullable|string|max:255',
            'badge_color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['priority_booking'] = $request->boolean('priority_booking');
        $validated['free_shipping'] = $request->boolean('free_shipping');
        $validated['is_active'] = $request->boolean('is_active', true);

        if (!empty($validated['features'])) {
            $validated['features'] = array_filter(
                array_map('trim', explode("\n", $validated['features']))
            );
        } else {
            $validated['features'] = [];
        }

        $membership->update($validated);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership tier updated successfully.');
    }

    /**
     * Delete a membership tier
     */
    public function destroy(MembershipTier $membership)
    {
        if ($membership->activeMembers()->count() > 0) {
            return redirect()->route('admin.memberships.index')
                ->with('error', 'Cannot delete tier with active members. Deactivate it instead.');
        }

        $membership->delete();

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership tier deleted.');
    }

    /**
     * Toggle tier active status
     */
    public function toggleActive(MembershipTier $tier)
    {
        $tier->update(['is_active' => !$tier->is_active]);

        $status = $tier->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.memberships.index')
            ->with('success', "Tier \"{$tier->name}\" {$status}.");
    }

    /**
     * Export members to CSV
     */
    public function export()
    {
        $filename = 'members-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Tier', 'Status', 'Started', 'Expires', 'Cancelled']);

            Membership::with(['customer', 'tier'])
                ->orderBy('created_at', 'desc')
                ->chunk(500, function ($members) use ($handle) {
                    foreach ($members as $member) {
                        fputcsv($handle, [
                            $member->customer->name ?? 'N/A',
                            $member->customer->email ?? '',
                            $member->tier->name ?? 'N/A',
                            ucfirst($member->status),
                            $member->starts_at?->format('Y-m-d'),
                            $member->expires_at?->format('Y-m-d'),
                            $member->cancelled_at?->format('Y-m-d'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
