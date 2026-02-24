<?php

namespace App\Console\Commands;

use App\Mail\WinBackMail;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWinBackEmails extends Command
{
    protected $signature = 'customers:send-win-back-emails';

    protected $description = 'Send 2-step win-back email sequence to inactive customers (60 days, 90 days)';

    public function handle(): int
    {
        $totalSent = 0;

        // Step 1: No order in 60+ days, no win-back email sent yet
        $totalSent += $this->sendStep(1,
            Customer::where('role', 'customer')
                ->whereNotNull('password')
                ->whereNull('win_back_email_sent_at')
                ->whereHas('orders', function ($q) {
                    $q->where('payment_status', 'paid');
                })
                ->whereDoesntHave('orders', function ($q) {
                    $q->where('payment_status', 'paid')
                      ->where('created_at', '>', now()->subDays(60));
                })
                ->get()
        );

        // Step 2: 90+ days inactive, step 1 sent, step 2 not sent
        $totalSent += $this->sendStep(2,
            Customer::where('role', 'customer')
                ->whereNotNull('password')
                ->whereNotNull('win_back_email_sent_at')
                ->whereNull('win_back_email_2_sent_at')
                ->where('win_back_email_sent_at', '<=', now()->subDays(30))
                ->whereDoesntHave('orders', function ($q) {
                    $q->where('payment_status', 'paid')
                      ->where('created_at', '>', now()->subDays(90));
                })
                ->get()
        );

        $this->info("Sent {$totalSent} win-back email(s) total.");

        return Command::SUCCESS;
    }

    private function sendStep(int $step, Collection $customers): int
    {
        if ($customers->isEmpty()) {
            $this->info("Step {$step}: No customers to email.");
            return 0;
        }

        $field = match ($step) {
            1 => 'win_back_email_sent_at',
            2 => 'win_back_email_2_sent_at',
        };

        $sent = 0;

        foreach ($customers as $customer) {
            if (!$customer->email) {
                continue;
            }

            try {
                Mail::to($customer->email)->send(new WinBackMail($customer, $step));
                $customer->update([$field => now()]);
                $sent++;
                $this->info("Step {$step} sent to {$customer->email}");
            } catch (\Exception $e) {
                Log::error("Win-back step {$step} email failed", [
                    'customer_id' => $customer->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Step {$step} failed for {$customer->email}: {$e->getMessage()}");
            }
        }

        $this->info("Step {$step}: Sent {$sent} email(s).");

        return $sent;
    }
}
