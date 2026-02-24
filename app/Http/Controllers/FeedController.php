<?php

namespace App\Http\Controllers;

use App\Services\GoogleShoppingFeedService;
use Illuminate\Support\Facades\Cache;

class FeedController extends Controller
{
    /**
     * Google Shopping XML product feed for Merchant Center
     */
    public function googleShopping(GoogleShoppingFeedService $feedService)
    {
        $xml = Cache::remember('google_shopping_feed', 3600, function () use ($feedService) {
            return $feedService->generateFeed();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
