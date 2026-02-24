<?php

namespace App\Console\Commands;

use App\Mail\ReviewRequestMail;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReviewRequests extends Command
{
    protected $signature = 'orders:send-review-requests';

    protected $description = 'Send review request emails for orders completed 7 days ago';

    public function handle()
    {
        $targetDate = Carbon::now()->subDays(7)->toDateString();

        $orders = Order::with(['items.item', 'customer'])
            ->where('fulfillment_status', 'completed')
            ->where('payment_status', 'paid')
            ->whereNull('review_request_sent_at')
            ->whereDate('updated_at', '<=', $targetDate)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders eligible for review requests.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($orders as $order) {
            // Skip if customer has already reviewed all items in this order
            $unreviewedItems = $order->items->filter(function ($item) use ($order) {
                return !Review::where('customer_id', $order->customer_id)
                    ->where('reviewable_type', $item->item_type)
                    ->where('reviewable_id', $item->item_id)
                    ->exists();
            });

            if ($unreviewedItems->isEmpty()) {
                $order->update(['review_request_sent_at' => now()]);
                $skipped++;
                continue;
            }

            try {
                Mail::to($order->customer->email)
                    ->send(new ReviewRequestMail($order));

                $order->update(['review_request_sent_at' => now()]);
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Review request email failed', [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Review requests: {$sent} sent, {$skipped} skipped (already reviewed), {$failed} failed.");

        return Command::SUCCESS;
    }
}
