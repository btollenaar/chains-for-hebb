<?php

namespace App\Http\Controllers;

use App\Services\FundraisingService;

class FundraisingController extends Controller
{
    public function __construct(
        protected FundraisingService $fundraisingService
    ) {}

    /**
     * Show the full progress page.
     */
    public function index()
    {
        $progressData = $this->fundraisingService->getProgressData();
        $milestones = $this->fundraisingService->getMilestones();
        $breakdown = $this->fundraisingService->getBreakdown();
        $revenueBreakdown = $this->fundraisingService->getRevenueBreakdown();

        return view('progress.index', compact('progressData', 'milestones', 'breakdown', 'revenueBreakdown'));
    }
}
