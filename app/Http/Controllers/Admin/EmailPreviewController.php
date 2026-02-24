<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AbandonedCartMail;
use App\Mail\AbandonedCartSequenceMail;
use App\Mail\BackInStockMail;
use App\Mail\ClaimAccountMail;
use App\Mail\LowStockAlertMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PostPurchaseFollowUpMail;
use App\Mail\ReviewRequestMail;
use App\Mail\ReturnStatusMail;
use App\Mail\WelcomeMail;
use App\Mail\WelcomeSequenceMail;
use App\Mail\WinBackMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;

class EmailPreviewController extends Controller
{
    private function templates(): array
    {
        return [
            'welcome' => ['name' => 'Welcome Email', 'icon' => 'fa-hand-wave', 'category' => 'Onboarding'],
            'welcome-sequence' => ['name' => 'Welcome Sequence', 'icon' => 'fa-envelopes-bulk', 'category' => 'Onboarding'],
            'order-confirmation' => ['name' => 'Order Confirmation', 'icon' => 'fa-receipt', 'category' => 'Orders'],
            'order-status-update' => ['name' => 'Order Status Update', 'icon' => 'fa-truck', 'category' => 'Orders'],
            'abandoned-cart' => ['name' => 'Abandoned Cart', 'icon' => 'fa-cart-shopping', 'category' => 'Recovery'],
            'abandoned-cart-sequence' => ['name' => 'Abandoned Cart Sequence', 'icon' => 'fa-cart-arrow-down', 'category' => 'Recovery'],
            'win-back' => ['name' => 'Win-Back', 'icon' => 'fa-rotate-left', 'category' => 'Recovery'],
            'review-request' => ['name' => 'Review Request', 'icon' => 'fa-star', 'category' => 'Engagement'],
            'post-purchase-follow-up' => ['name' => 'Post-Purchase Follow-Up', 'icon' => 'fa-heart', 'category' => 'Engagement'],
            'low-stock-alert' => ['name' => 'Low Stock Alert', 'icon' => 'fa-exclamation-triangle', 'category' => 'Admin'],
            'back-in-stock' => ['name' => 'Back in Stock', 'icon' => 'fa-box', 'category' => 'Notifications'],
            'claim-account' => ['name' => 'Claim Account', 'icon' => 'fa-user-plus', 'category' => 'Onboarding'],
            'return-status' => ['name' => 'Return Status', 'icon' => 'fa-undo', 'category' => 'Orders'],
        ];
    }

    public function index()
    {
        $templates = $this->templates();
        $grouped = collect($templates)
            ->map(fn($t, $key) => array_merge($t, ['slug' => $key]))
            ->groupBy(fn($t) => $t['category']);

        return view('admin.email-previews.index', compact('templates', 'grouped'));
    }

    public function preview(string $template)
    {
        $templates = $this->templates();
        if (!isset($templates[$template])) {
            abort(404);
        }

        $customer = Customer::first() ?? new Customer(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
        $order = Order::with('items')->first();
        $product = Product::first() ?? new Product(['name' => 'Sample Product', 'price' => 29.99]);

        $mailable = match ($template) {
            'welcome' => new WelcomeMail($customer),
            'welcome-sequence' => new WelcomeSequenceMail($customer, 2),
            'order-confirmation' => $order ? new OrderConfirmationMail($order) : null,
            'order-status-update' => $order ? new OrderStatusUpdateMail($order, 'shipped') : null,
            'abandoned-cart' => new AbandonedCartMail($customer),
            'abandoned-cart-sequence' => new AbandonedCartSequenceMail($customer, 2),
            'win-back' => new WinBackMail($customer),
            'review-request' => $order ? new ReviewRequestMail($order) : null,
            'post-purchase-follow-up' => $order ? new PostPurchaseFollowUpMail($order) : null,
            'low-stock-alert' => new LowStockAlertMail(collect([$product])),
            'back-in-stock' => new BackInStockMail($customer, $product),
            'claim-account' => new ClaimAccountMail($customer),
            'return-status' => null,
            default => null,
        };

        if (!$mailable) {
            return response('<html><body style="font-family:sans-serif;padding:40px;text-align:center;color:#666;"><p>Preview requires sample data that is not available. Seed the database first.</p></body></html>');
        }

        return response($mailable->render());
    }
}
