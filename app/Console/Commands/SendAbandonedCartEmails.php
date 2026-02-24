<?php

namespace App\Console\Commands;

use App\Mail\AbandonedCartSequenceMail;
use App\Models\Cart;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmails extends Command
{
    protected $signature = 'cart:send-abandoned-emails';

    protected $description = 'Send 3-step abandoned cart email sequence (1h, 24h, 72h)';

    public function handle(): int
    {
        $totalSent = 0;

        // Step 1: Cart abandoned 1+ hour ago, no email sent yet
        $totalSent += $this->sendStep(1);

        // Step 2: Cart abandoned 24+ hours ago, step 1 sent, step 2 not sent
        $totalSent += $this->sendStep(2);

        // Step 3: Cart abandoned 72+ hours ago, step 2 sent, step 3 not sent
        $totalSent += $this->sendStep(3);

        $this->info("Sent {$totalSent} abandoned cart email(s) total.");

        return Command::SUCCESS;
    }

    private function sendStep(int $step): int
    {
        $delays = [1 => 1, 2 => 24, 3 => 72];

        $sentField = match ($step) {
            1 => 'abandoned_cart_email_sent_at',
            2 => 'abandoned_cart_email_2_sent_at',
            3 => 'abandoned_cart_email_3_sent_at',
        };

        $prevField = match ($step) {
            1 => null,
            2 => 'abandoned_cart_email_sent_at',
            3 => 'abandoned_cart_email_2_sent_at',
        };

        $query = Cart::whereNotNull('customer_id')
            ->where('updated_at', '<', now()->subHours($delays[$step]))
            ->where('updated_at', '>', now()->subDays(7))
            ->whereNull($sentField);

        if ($prevField) {
            $query->whereNotNull($prevField);
        }

        $customerIds = $query->distinct()->pluck('customer_id');

        if ($customerIds->isEmpty()) {
            $this->info("Step {$step}: No abandoned carts to email.");
            return 0;
        }

        $sent = 0;

        foreach ($customerIds as $customerId) {
            $customer = Customer::find($customerId);
            if (!$customer?->email) {
                continue;
            }

            // Skip if customer placed a recent order
            if ($customer->orders()->where('created_at', '>', now()->subHour())->exists()) {
                continue;
            }

            $cartItems = Cart::with('item')->where('customer_id', $customerId)->get();
            if ($cartItems->isEmpty()) {
                continue;
            }

            $cartTotal = $cartItems->sum(function ($cartItem) {
                $price = $cartItem->item->current_price ?? $cartItem->item->base_price ?? $cartItem->item->price ?? 0;
                return $price * $cartItem->quantity;
            });

            try {
                Mail::to($customer->email)->send(new AbandonedCartSequenceMail($customer, $cartItems, $cartTotal, $step));

                Cart::where('customer_id', $customerId)->update([$sentField => now()]);

                $sent++;
                $this->info("Step {$step} sent to {$customer->email}");
            } catch (\Exception $e) {
                Log::error("Abandoned cart step {$step} email failed", [
                    'customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Step {$step} failed for {$customer->email}: {$e->getMessage()}");
            }
        }

        $this->info("Step {$step}: Sent {$sent} email(s).");

        return $sent;
    }
}
