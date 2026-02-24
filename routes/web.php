<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AccountClaimController;
use App\Http\Controllers\Admin\AboutController as AdminAboutController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\NewsletterCampaignController;
use App\Http\Controllers\Admin\SubscriberListController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FundraisingController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\ReviewController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CouponController as ApiCouponController;
use App\Http\Controllers\Store\ReturnController;
use App\Http\Controllers\Store\WishlistController;
use App\Http\Controllers\Admin\ReturnController as AdminReturnController;
use App\Http\Controllers\NewsletterSubscribeController;
use App\Http\Controllers\NewsletterUnsubscribeController;
use App\Http\Controllers\NewsletterTrackingController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\Admin\PrintfulCatalogController;
use App\Http\Controllers\PrintfulWebhookController;
use App\Http\Controllers\Store\AddressController;
use App\Http\Controllers\Store\StockNotificationController;
use App\Http\Controllers\Admin\CsvImportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Store\DataExportController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\EmailPreviewController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\Admin\DonationTierController as AdminDonationTierController;
use App\Http\Controllers\Admin\CmsPageController as AdminCmsPageController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\SponsorController as AdminSponsorController;
use App\Http\Controllers\Admin\SponsorTierController as AdminSponsorTierController;
use App\Http\Controllers\Admin\FundraisingController as AdminFundraisingController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use Illuminate\Support\Facades\Route;

// Stripe Webhook (must be excluded from CSRF verification)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Printful Webhook (must be excluded from CSRF verification)
Route::post('/printful/webhook', [PrintfulWebhookController::class, 'handleWebhook'])->name('printful.webhook');

// XML Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Public Order Tracking Lookup
Route::get('/track', [\App\Http\Controllers\Store\OrderController::class, 'trackForm'])->name('track.form');
Route::post('/track', [\App\Http\Controllers\Store\OrderController::class, 'trackLookup'])->name('track.lookup');

// Health Check
Route::get('/health', HealthController::class)->name('health');

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// About
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Legal Pages
Route::get('/privacy-policy', [LegalPageController::class, 'privacyPolicy'])->name('legal.privacy-policy');
Route::get('/terms-of-service', [LegalPageController::class, 'termsOfService'])->name('legal.terms-of-service');
Route::get('/return-policy', [LegalPageController::class, 'returnPolicy'])->name('legal.return-policy');
Route::get('/shipping-policy', [LegalPageController::class, 'shippingPolicy'])->name('legal.shipping-policy');

// =====================================================================
// Donations (public)
// =====================================================================
Route::get('/donate', [DonationController::class, 'index'])->name('donate.index');
Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');
Route::get('/donate/success/{donation}', [DonationController::class, 'success'])
    ->middleware('signed')
    ->name('donate.success');
Route::get('/donate/wall', [DonationController::class, 'wall'])->name('donate.wall');

// =====================================================================
// Events (public)
// =====================================================================
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp'])->name('events.rsvp');
Route::get('/events/rsvp/{token}/cancel', [EventController::class, 'cancelRsvp'])->name('events.rsvp.cancel');

// =====================================================================
// Gallery (public)
// =====================================================================
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');

// =====================================================================
// Fundraising Progress (public)
// =====================================================================
Route::get('/progress', [FundraisingController::class, 'index'])->name('progress.index');

// =====================================================================
// Sponsors (public)
// =====================================================================
Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');

// =====================================================================
// CMS Pages (public — must be after all other routes to avoid slug conflicts)
// =====================================================================
Route::get('/pages/{page}', [CmsPageController::class, 'show'])->name('pages.show');

// Products (Shop / Merch)
Route::prefix('shop')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/category/{category}', [ProductController::class, 'category'])->name('category');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// Blog (News/Updates)
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// Stock Notifications (public)
Route::post('/stock-notifications', [StockNotificationController::class, 'store'])->name('stock-notifications.store');

// Newsletter Public Routes
Route::post('/newsletter/subscribe', [NewsletterSubscribeController::class, 'subscribe'])->name('newsletter.subscribe');

Route::prefix('newsletter')->name('newsletter.')->group(function () {
    Route::get('/unsubscribe', [NewsletterUnsubscribeController::class, 'show'])->name('unsubscribe');
    Route::post('/unsubscribe', [NewsletterUnsubscribeController::class, 'unsubscribe']);
    Route::get('/track/open', [NewsletterTrackingController::class, 'trackOpen'])->name('track.open')->middleware('throttle:120,1');
    Route::get('/track/click', [NewsletterTrackingController::class, 'trackClick'])->name('track.click')->middleware('throttle:120,1');
});

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

// API Routes (AJAX endpoints)
Route::prefix('api')->name('api.')->middleware('throttle:60,1')->group(function () {
    Route::get('/cart/count', [ApiCartController::class, 'count'])->name('cart.count');
    Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');
    Route::post('/coupon/validate', [ApiCouponController::class, 'validate'])->name('coupon.validate');
});

// Dashboard Route
Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Breeze Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Checkout (allows guest checkout)
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])
        ->middleware('signed')
        ->name('success');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

// Account Claim (for guest checkout customers)
Route::middleware('signed')->prefix('account')->name('account.')->group(function () {
    Route::get('/claim/{customer}', [AccountClaimController::class, 'show'])->name('claim.show');
    Route::post('/claim/{customer}', [AccountClaimController::class, 'store'])->name('claim.store');
});

// Orders (requires authentication)
Route::middleware(['auth'])->prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Store\OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [\App\Http\Controllers\Store\OrderController::class, 'show'])->name('show');
    Route::get('/{order}/invoice', [\App\Http\Controllers\Store\OrderController::class, 'downloadInvoice'])->name('invoice');
    Route::post('/{order}/reorder', [\App\Http\Controllers\Store\OrderController::class, 'reorder'])->name('reorder');
    Route::get('/{order}/tracking', [\App\Http\Controllers\Store\OrderController::class, 'tracking'])->name('tracking');
});

// Wishlist (requires authentication)
Route::middleware(['auth'])->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::post('/{wishlist}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
    Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('destroy');
});

// Addresses (requires authentication)
Route::middleware(['auth'])->prefix('addresses')->name('addresses.')->group(function () {
    Route::get('/', [AddressController::class, 'index'])->name('index');
    Route::get('/json', [AddressController::class, 'jsonIndex'])->name('json');
    Route::post('/', [AddressController::class, 'store'])->name('store');
    Route::put('/{address}', [AddressController::class, 'update'])->name('update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    Route::post('/{address}/default', [AddressController::class, 'setDefault'])->name('set-default');
});

// Reviews (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::post('products/{product}/reviews', [ReviewController::class, 'storeProductReview'])->name('products.reviews.store');
    Route::post('reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');
    Route::post('reviews/{review}/not-helpful', [ReviewController::class, 'markNotHelpful'])->name('reviews.not-helpful');
});

// Data Export (GDPR)
Route::middleware(['auth'])->group(function () {
    Route::post('data-export/request', [DataExportController::class, 'request'])->name('data-export.request');
    Route::get('data-export/{export}/download', [DataExportController::class, 'download'])->name('data-export.download');
});

// =====================================================================
// Admin Routes (requires auth + admin middleware)
// =====================================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Email Template Previews
    Route::get('/email-previews', [EmailPreviewController::class, 'index'])->name('email-previews.index');
    Route::get('/email-previews/{template}', [EmailPreviewController::class, 'preview'])->name('email-previews.preview');

    // =====================================================================
    // Fundraising Admin
    // =====================================================================

    // Donations
    Route::get('/donations', [AdminDonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [AdminDonationController::class, 'show'])->name('donations.show');

    // Donation Tiers
    Route::resource('donation-tiers', AdminDonationTierController::class);

    // Fundraising Progress (milestones + breakdown)
    Route::get('/fundraising', [AdminFundraisingController::class, 'index'])->name('fundraising.index');
    Route::get('/fundraising/milestones/create', [AdminFundraisingController::class, 'createMilestone'])->name('fundraising.milestones.create');
    Route::post('/fundraising/milestones', [AdminFundraisingController::class, 'storeMilestone'])->name('fundraising.milestones.store');
    Route::get('/fundraising/milestones/{milestone}/edit', [AdminFundraisingController::class, 'editMilestone'])->name('fundraising.milestones.edit');
    Route::put('/fundraising/milestones/{milestone}', [AdminFundraisingController::class, 'updateMilestone'])->name('fundraising.milestones.update');
    Route::delete('/fundraising/milestones/{milestone}', [AdminFundraisingController::class, 'destroyMilestone'])->name('fundraising.milestones.destroy');
    Route::get('/fundraising/breakdowns/create', [AdminFundraisingController::class, 'createBreakdown'])->name('fundraising.breakdowns.create');
    Route::post('/fundraising/breakdowns', [AdminFundraisingController::class, 'storeBreakdown'])->name('fundraising.breakdowns.store');
    Route::get('/fundraising/breakdowns/{breakdown}/edit', [AdminFundraisingController::class, 'editBreakdown'])->name('fundraising.breakdowns.edit');
    Route::put('/fundraising/breakdowns/{breakdown}', [AdminFundraisingController::class, 'updateBreakdown'])->name('fundraising.breakdowns.update');
    Route::delete('/fundraising/breakdowns/{breakdown}', [AdminFundraisingController::class, 'destroyBreakdown'])->name('fundraising.breakdowns.destroy');

    // Sponsors
    Route::resource('sponsors', AdminSponsorController::class);
    Route::resource('sponsor-tiers', AdminSponsorTierController::class);

    // =====================================================================
    // Content Admin
    // =====================================================================

    // CMS Pages
    Route::resource('pages', AdminCmsPageController::class);

    // Events
    Route::resource('events', AdminEventController::class);
    Route::get('/events/{event}/rsvps/export', [AdminEventController::class, 'exportRsvps'])->name('events.rsvps.export');

    // Gallery
    Route::resource('gallery', AdminGalleryController::class);
    Route::post('/gallery/{album}/photos', [AdminGalleryController::class, 'uploadPhotos'])->name('gallery.photos.upload');
    Route::delete('/gallery/{album}/photos/{photo}', [AdminGalleryController::class, 'destroyPhoto'])->name('gallery.photos.destroy');

    // =====================================================================
    // Catalog Admin (existing)
    // =====================================================================

    // Printful Catalog Management
    Route::prefix('printful')->name('printful.')->group(function () {
        Route::get('/catalog', [PrintfulCatalogController::class, 'index'])->name('catalog');
        Route::get('/catalog/{printfulProductId}/setup', [PrintfulCatalogController::class, 'setup'])->name('setup');
        Route::post('/catalog/store', [PrintfulCatalogController::class, 'store'])->name('store');
        Route::post('/catalog/sync', [PrintfulCatalogController::class, 'syncCatalog'])->name('sync-catalog');
        Route::post('/products/{product}/design', [PrintfulCatalogController::class, 'uploadDesign'])->name('upload-design');
        Route::post('/products/{product}/mockups', [PrintfulCatalogController::class, 'generateMockups'])->name('generate-mockups');
    });

    // CSV Imports
    Route::get('imports', [CsvImportController::class, 'index'])->name('imports.index');
    Route::get('imports/create', [CsvImportController::class, 'create'])->name('imports.create');
    Route::post('imports', [CsvImportController::class, 'store'])->name('imports.store');
    Route::get('imports/template/{type}', [CsvImportController::class, 'downloadTemplate'])->name('imports.template');
    Route::get('imports/{import}', [CsvImportController::class, 'show'])->name('imports.show');
    Route::get('imports/{import}/progress', [CsvImportController::class, 'progress'])->name('imports.progress');
    Route::get('imports/{import}/errors', [CsvImportController::class, 'downloadErrors'])->name('imports.errors');

    // About Page
    Route::get('/about/edit', [AdminAboutController::class, 'edit'])->name('about.edit');
    Route::put('/about', [AdminAboutController::class, 'update'])->name('about.update');

    // Product Category Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::resource('categories', ProductCategoryController::class)->except('show');
    });

    // Export Functionality
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/customers/export', [AdminCustomerController::class, 'export'])->name('customers.export');
    Route::get('/products/export', [AdminProductController::class, 'export'])->name('products.export');

    // Bulk Actions
    Route::post('/products/bulk', [AdminProductController::class, 'bulkAction'])->name('products.bulk');
    Route::post('/blog/posts/bulk', [AdminBlogPostController::class, 'bulkAction'])->name('blog.posts.bulk');

    // Product Variant Management
    Route::patch('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('products.variants.update');
    Route::post('/products/{product}/variants/bulk-update', [ProductVariantController::class, 'bulkUpdate'])->name('products.variants.bulk-update');

    Route::resource('products', AdminProductController::class);
    Route::resource('orders', AdminOrderController::class);

    Route::resource('customers', AdminCustomerController::class)->only(['index', 'show']);

    // Tag Management
    Route::resource('tags', AdminTagController::class)->except(['show']);
    Route::post('tags/assign', [AdminTagController::class, 'assignToCustomer'])->name('tags.assign');
    Route::post('tags/bulk-assign', [AdminTagController::class, 'bulkAssign'])->name('tags.bulk-assign');

    // Blog Management
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::resource('categories', BlogCategoryController::class)->except('show');
        Route::resource('posts', AdminBlogPostController::class);
    });

    // Coupon Management
    Route::get('/coupons/export', [AdminCouponController::class, 'export'])->name('coupons.export');
    Route::post('/coupons/{coupon}/toggle-active', [AdminCouponController::class, 'toggleActive'])->name('coupons.toggle-active');
    Route::resource('coupons', AdminCouponController::class);

    // Review Management
    Route::resource('reviews', AdminReviewController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::post('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');

    // Return Management
    Route::get('/returns', [AdminReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{return}', [AdminReturnController::class, 'show'])->name('returns.show');
    Route::post('/returns/{return}/approve', [AdminReturnController::class, 'approve'])->name('returns.approve');
    Route::post('/returns/{return}/reject', [AdminReturnController::class, 'reject'])->name('returns.reject');
    Route::post('/returns/{return}/complete', [AdminReturnController::class, 'complete'])->name('returns.complete');

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [AdminNotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Settings Management
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.update.profile');
    Route::put('/settings/contact', [AdminSettingsController::class, 'updateContact'])->name('settings.update.contact');
    Route::put('/settings/social', [AdminSettingsController::class, 'updateSocial'])->name('settings.update.social');
    Route::put('/settings/branding', [AdminSettingsController::class, 'updateBranding'])->name('settings.update.branding');
    Route::put('/settings/features', [AdminSettingsController::class, 'updateFeatures'])->name('settings.update.features');
    Route::put('/settings/theme', [AdminSettingsController::class, 'updateTheme'])->name('settings.update.theme');
    Route::put('/settings/homepage', [AdminSettingsController::class, 'updateHomepage'])->name('settings.update.homepage');
    Route::put('/settings/navigation', [AdminSettingsController::class, 'updateNavigation'])->name('settings.update.navigation');

    // Audit Log
    Route::get('audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');

    // Newsletter Subscribers
    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])->name('newsletter.index');
    Route::get('/newsletter/export', [AdminNewsletterController::class, 'export'])->name('newsletter.export');
    Route::put('/newsletter/{subscription}/activate', [AdminNewsletterController::class, 'activate'])->name('newsletter.activate');
    Route::put('/newsletter/{subscription}/deactivate', [AdminNewsletterController::class, 'deactivate'])->name('newsletter.deactivate');
    Route::delete('/newsletter/{subscription}', [AdminNewsletterController::class, 'destroy'])->name('newsletter.destroy');

    // Newsletter Campaigns
    Route::prefix('newsletters/campaigns')->name('newsletters.campaigns.')->group(function () {
        Route::get('/', [NewsletterCampaignController::class, 'index'])->name('index');
        Route::get('/create', [NewsletterCampaignController::class, 'create'])->name('create');
        Route::post('/', [NewsletterCampaignController::class, 'store'])->name('store');
        Route::get('/{campaign}', [NewsletterCampaignController::class, 'show'])->name('show');
        Route::get('/{campaign}/edit', [NewsletterCampaignController::class, 'edit'])->name('edit');
        Route::put('/{campaign}', [NewsletterCampaignController::class, 'update'])->name('update');
        Route::delete('/{campaign}', [NewsletterCampaignController::class, 'destroy'])->name('destroy');
        Route::post('/{campaign}/duplicate', [NewsletterCampaignController::class, 'duplicate'])->name('duplicate');
        Route::post('/{campaign}/send-test', [NewsletterCampaignController::class, 'sendTest'])->name('send-test');
        Route::post('/{campaign}/cancel', [NewsletterCampaignController::class, 'cancel'])->name('cancel');
        Route::get('/{campaign}/preview', [NewsletterCampaignController::class, 'preview'])->name('preview');
    });

    // Subscriber Lists
    Route::resource('subscriber-lists', SubscriberListController::class);
    Route::delete('/subscriber-lists/{subscriberList}/subscribers/{subscriber}', [SubscriberListController::class, 'removeSubscriber'])
        ->name('subscriber-lists.remove-subscriber');
    Route::post('/subscriber-lists/{subscriberList}/subscribers/bulk-add', [SubscriberListController::class, 'bulkAddSubscribers'])
        ->name('subscriber-lists.bulk-add-subscribers');
    Route::post('/subscriber-lists/{subscriberList}/subscribers/bulk-remove', [SubscriberListController::class, 'bulkRemoveSubscribers'])
        ->name('subscriber-lists.bulk-remove-subscribers');
});

// Breeze Authentication Routes
require __DIR__.'/auth.php';
