<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\Product;
use App\Models\Sponsor;
use App\Services\EventService;
use App\Services\FundraisingService;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(
        FundraisingService $fundraisingService,
        EventService $eventService,
        GalleryService $galleryService
    ) {
        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->limit(6)
            ->get();

        $progressData = $fundraisingService->getProgressData();

        $upcomingEvents = $eventService->getUpcoming(3);

        $featuredSponsors = Sponsor::active()
            ->featured()
            ->with('sponsorTier')
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        $featuredPhotos = $galleryService->getFeaturedPhotos(6);

        $latestPosts = BlogPost::where('published', true)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('home', compact(
            'featuredProducts',
            'progressData',
            'upcomingEvents',
            'featuredSponsors',
            'featuredPhotos',
            'latestPosts'
        ));
    }
}
