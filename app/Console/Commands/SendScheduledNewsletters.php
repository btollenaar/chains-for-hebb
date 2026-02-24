<?php

namespace App\Console\Commands;

use App\Jobs\SendNewsletter;
use App\Models\Newsletter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNewsletters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled newsletters that are due to be sent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled newsletters...');

        // Find newsletters that are scheduled and due to be sent
        $newsletters = Newsletter::pendingSend()->get();

        if ($newsletters->isEmpty()) {
            $this->info('No newsletters scheduled for sending.');
            return 0;
        }

        $this->info("Found {$newsletters->count()} newsletter(s) ready to send.");

        foreach ($newsletters as $newsletter) {
            try {
                // Dispatch the SendNewsletter job
                SendNewsletter::dispatch($newsletter);

                $this->info("Dispatched newsletter: {$newsletter->subject} (ID: {$newsletter->id})");

                Log::info('Scheduled newsletter dispatched', [
                    'newsletter_id' => $newsletter->id,
                    'subject' => $newsletter->subject,
                    'scheduled_at' => $newsletter->scheduled_at,
                ]);

            } catch (\Exception $e) {
                $this->error("Failed to dispatch newsletter ID {$newsletter->id}: {$e->getMessage()}");

                Log::error('Failed to dispatch scheduled newsletter', [
                    'newsletter_id' => $newsletter->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Scheduled newsletter check complete.');

        return 0;
    }
}
