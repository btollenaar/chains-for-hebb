<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundraisingBreakdown;
use App\Models\FundraisingMilestone;
use App\Services\FundraisingService;
use Illuminate\Http\Request;

class FundraisingController extends Controller
{
    public function __construct(
        protected FundraisingService $fundraisingService
    ) {}

    public function index()
    {
        $progressData = $this->fundraisingService->getProgressData();
        $milestones = $this->fundraisingService->getMilestones();
        $breakdowns = $this->fundraisingService->getBreakdown();
        $revenueBreakdown = $this->fundraisingService->getRevenueBreakdown();

        return view('admin.fundraising.index', compact('progressData', 'milestones', 'breakdowns', 'revenueBreakdown'));
    }

    public function createMilestone()
    {
        return view('admin.fundraising.milestones.create');
    }

    public function editMilestone(FundraisingMilestone $milestone)
    {
        return view('admin.fundraising.milestones.edit', compact('milestone'));
    }

    public function storeMilestone(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        FundraisingMilestone::create($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Milestone added.');
    }

    public function updateMilestone(Request $request, FundraisingMilestone $milestone)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_amount' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:100',
            'is_reached' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_reached'] = $request->boolean('is_reached');
        if ($validated['is_reached'] && !$milestone->is_reached) {
            $validated['reached_at'] = now();
        }

        $milestone->update($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Milestone updated.');
    }

    public function destroyMilestone(FundraisingMilestone $milestone)
    {
        $milestone->delete();
        return redirect()->route('admin.fundraising.index')->with('success', 'Milestone deleted.');
    }

    public function createBreakdown()
    {
        return view('admin.fundraising.breakdowns.create');
    }

    public function editBreakdown(FundraisingBreakdown $breakdown)
    {
        return view('admin.fundraising.breakdowns.edit', compact('breakdown'));
    }

    public function storeBreakdown(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        FundraisingBreakdown::create($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Budget item added.');
    }

    public function updateBreakdown(Request $request, FundraisingBreakdown $breakdown)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        $breakdown->update($validated);

        return redirect()->route('admin.fundraising.index')->with('success', 'Budget item updated.');
    }

    public function destroyBreakdown(FundraisingBreakdown $breakdown)
    {
        $breakdown->delete();
        return redirect()->route('admin.fundraising.index')->with('success', 'Budget item deleted.');
    }
}
