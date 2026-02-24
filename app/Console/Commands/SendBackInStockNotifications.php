<?php

namespace App\Console\Commands;

use App\Mail\BackInStockMail;
use App\Models\Product;
use App\Models\StockNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBackInStockNotifications extends Command
{
    protected $signature = 'notifications:send-back-in-stock';
    protected $description = 'Send back-in-stock notifications for restocked products';

    public function handle(): int
    {
        $notifications = StockNotification::pending()
            ->with('product')
            ->get()
            ->groupBy('product_id');

        $sent = 0;

        foreach ($notifications as $productId => $productNotifications) {
            $product = $productNotifications->first()->product;

            if (!$product || $product->stock_quantity <= 0 || $product->status !== 'active') {
                continue;
            }

            foreach ($productNotifications as $notification) {
                try {
                    Mail::to($notification->email)->send(new BackInStockMail($product));
                    $notification->update(['notified_at' => now()]);
                    $sent++;
                } catch (\Exception $e) {
                    Log::error('Back-in-stock notification failed', [
                        'notification_id' => $notification->id,
                        'email' => $notification->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Sent {$sent} back-in-stock notifications.");
        return self::SUCCESS;
    }
}
