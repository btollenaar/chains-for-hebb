<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\FundraisingBreakdown;
use App\Models\FundraisingMilestone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sponsor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FundraisingService
{
    /**
     * Get total amount raised from all sources.
     * Cached for 1 hour.
     */
    public function getTotalRaised(): float
    {
        return Cache::remember('fundraising_total_raised', 3600, function () {
            return $this->getDonationTotal()
                 + $this->getSponsorTotal()
                 + $this->getMerchProfit();
        });
    }

    /**
     * Get fundraising goal amount.
     */
    public function getGoalAmount(): float
    {
        return (float) config('business.fundraising.goal_amount', 15000.00);
    }

    /**
     * Get progress percentage (0-100).
     */
    public function getProgressPercentage(): float
    {
        $goal = $this->getGoalAmount();
        if ($goal <= 0) return 0;

        return min(100, round(($this->getTotalRaised() / $goal) * 100, 1));
    }

    /**
     * Get total from paid donations.
     */
    public function getDonationTotal(): float
    {
        return (float) Donation::paid()->sum('amount');
    }

    /**
     * Get total from sponsor contributions.
     */
    public function getSponsorTotal(): float
    {
        return (float) Sponsor::active()->sum('sponsorship_amount');
    }

    /**
     * Get merch profit (retail price - printful cost) from paid orders.
     */
    public function getMerchProfit(): float
    {
        return (float) OrderItem::whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->whereNotNull('variant_id')
            ->get()
            ->sum(function ($item) {
                $variant = ProductVariant::find($item->variant_id);
                if (!$variant) return 0;
                return ($item->price - ($variant->printful_cost ?? 0)) * $item->quantity;
            });
    }

    /**
     * Get revenue breakdown by source.
     */
    public function getRevenueBreakdown(): array
    {
        return [
            'donations' => $this->getDonationTotal(),
            'sponsors' => $this->getSponsorTotal(),
            'merch_profit' => $this->getMerchProfit(),
            'total' => $this->getTotalRaised(),
        ];
    }

    /**
     * Get all milestones with progress indicators.
     */
    public function getMilestones(): \Illuminate\Database\Eloquent\Collection
    {
        return FundraisingMilestone::ordered()->get();
    }

    /**
     * Check and update milestone statuses.
     */
    public function checkMilestones(): void
    {
        $totalRaised = $this->getTotalRaised();

        FundraisingMilestone::unreached()
            ->where('target_amount', '<=', $totalRaised)
            ->each(function ($milestone) {
                $milestone->update([
                    'is_reached' => true,
                    'reached_at' => now(),
                ]);
            });
    }

    /**
     * Get budget breakdown items.
     */
    public function getBreakdown(): \Illuminate\Database\Eloquent\Collection
    {
        return FundraisingBreakdown::ordered()->get();
    }

    /**
     * Get data for the progress bar component.
     */
    public function getProgressData(): array
    {
        $totalRaised = $this->getTotalRaised();
        $goal = $this->getGoalAmount();
        $revenueBreakdown = $this->getRevenueBreakdown();
        $nextMilestone = FundraisingMilestone::unreached()
            ->ordered()
            ->first();

        return [
            'total_raised' => $totalRaised,
            'goal' => $goal,
            'percentage' => $this->getProgressPercentage(),
            'next_milestone' => $nextMilestone,
            'breakdown' => $revenueBreakdown,
            'donations_total' => $revenueBreakdown['donations'],
            'sponsors_total' => $revenueBreakdown['sponsors'],
            'merch_profit' => $revenueBreakdown['merch_profit'],
        ];
    }

    /**
     * Clear fundraising cache (call after donations, orders, or sponsor changes).
     */
    public function clearCache(): void
    {
        Cache::forget('fundraising_total_raised');
    }
}
