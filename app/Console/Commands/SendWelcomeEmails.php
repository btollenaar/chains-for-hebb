<?php

namespace App\Console\Commands;

use App\Mail\WelcomeSequenceMail;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmails extends Command
{
    protected $signature = 'customers:send-welcome-emails';

    protected $description = 'Send 3-step welcome drip sequence to new customers (immediate, day 3, day 7)';

    public function handle(): int
    {
        $totalSent = 0;

        // Step 1: New registrations (within last 24h, no welcome sent yet)
        $totalSent += $this->sendStep(1,
            Customer::where('role', 'customer')
                ->whereNull('welcome_email_sent_at')
                ->where('created_at', '>', now()->subDay())
                ->whereNotNull('password')
                ->get()
        );

        // Step 2: 3+ days after registration, step 1 sent, step 2 not sent
        $totalSent += $this->sendStep(2,
            Customer::where('role', 'customer')
                ->whereNotNull('welcome_email_sent_at')
                ->whereNull('welcome_email_2_sent_at')
                ->where('created_at', '<=', now()->subDays(3))
                ->whereNotNull('password')
                ->get()
        );

        // Step 3: 7+ days after registration, step 2 sent, step 3 not sent
        $totalSent += $this->sendStep(3,
            Customer::where('role', 'customer')
                ->whereNotNull('welcome_email_2_sent_at')
                ->whereNull('welcome_email_3_sent_at')
                ->where('created_at', '<=', now()->subDays(7))
                ->whereNotNull('password')
                ->get()
        );

        $this->info("Sent {$totalSent} welcome email(s) total.");

        return Command::SUCCESS;
    }

    private function sendStep(int $step, Collection $customers): int
    {
        if ($customers->isEmpty()) {
            $this->info("Step {$step}: No customers to email.");
            return 0;
        }

        $field = match ($step) {
            1 => 'welcome_email_sent_at',
            2 => 'welcome_email_2_sent_at',
            3 => 'welcome_email_3_sent_at',
        };

        $sent = 0;

        foreach ($customers as $customer) {
            if (!$customer->email) {
                continue;
            }

            try {
                Mail::to($customer->email)->send(new WelcomeSequenceMail($customer, $step));
                $customer->update([$field => now()]);
                $sent++;
                $this->info("Step {$step} sent to {$customer->email}");
            } catch (\Exception $e) {
                Log::error("Welcome step {$step} email failed", [
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
