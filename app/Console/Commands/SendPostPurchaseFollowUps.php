<?php

namespace App\Console\Commands;

use App\Mail\PostPurchaseFollowUpMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPostPurchaseFollowUps extends Command
{
    protected $signature = 'orders:send-post-purchase-follow-ups';

    protected $description = 'Send follow-up emails 30 days after order delivery encouraging repeat purchase';

    public function handle(): int
    {
        $orders = Order::with('customer')
            ->where('fulfillment_status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', now()->subDays(30))
            ->where('delivered_at', '>', now()->subDays(31)) // Only target the 30-day window
            ->whereNull('post_purchase_email_sent_at')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders ready for post-purchase follow-up.');
            return Command::SUCCESS;
        }

        $sent = 0;

        foreach ($orders as $order) {
            if (!$order->customer || !$order->customer->email) {
                continue;
            }

            try {
                Mail::to($order->customer->email)->send(new PostPurchaseFollowUpMail($order));

                $order->update(['post_purchase_email_sent_at' => now()]);

                $sent++;
                $this->info("Post-purchase follow-up sent for Order #{$order->order_number}");
            } catch (\Exception $e) {
                Log::error('Post-purchase follow-up email failed', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed for Order #{$order->order_number}: {$e->getMessage()}");
            }
        }

        $this->info("Sent {$sent} post-purchase follow-up email(s).");

        return Command::SUCCESS;
    }
}
