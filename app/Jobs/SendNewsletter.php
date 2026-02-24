<?php

namespace App\Jobs;

use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletter implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600; // 1 hour
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Newsletter $newsletter
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mark newsletter as sending
        $this->newsletter->update([
            'status' => 'sending',
            'started_sending_at' => now(),
        ]);

        try {
            // Get all target subscribers from selected lists (distinct, active only)
            $subscribers = NewsletterSubscription::active()
                ->whereHas('lists', function ($q) {
                    $q->whereIn('subscriber_list_id', $this->newsletter->lists->pluck('id'));
                })
                ->distinct()
                ->get();

            // Bulk create newsletter_sends records with unique tracking tokens
            $sends = [];
            foreach ($subscribers as $subscriber) {
                $sends[] = [
                    'newsletter_id' => $this->newsletter->id,
                    'newsletter_subscription_id' => $subscriber->id,
                    'status' => 'pending',
                    'tracking_token' => \Illuminate\Support\Str::random(64),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert in chunks to avoid memory issues
            foreach (array_chunk($sends, 500) as $chunk) {
                NewsletterSend::insert($chunk);
            }

            // Get batch size and delay from config (with defaults)
            $batchSize = config('newsletter.batch_size', 100);
            $batchDelay = config('newsletter.batch_delay', 60); // seconds

            // Get all pending sends for this newsletter
            $pendingSends = $this->newsletter->sends()->where('status', 'pending')->get();

            // Send in chunks
            foreach ($pendingSends->chunk($batchSize) as $index => $chunk) {
                foreach ($chunk as $send) {
                    try {
                        // Load subscription relationship
                        $send->load('subscription');

                        // Send the email
                        Mail::to($send->subscription->email)
                            ->send(new NewsletterMail($this->newsletter, $send, false));

                        // Mark as sent
                        $send->markAsSent();

                    } catch (\Exception $e) {
                        // Mark as failed and log error
                        $send->markAsFailed($e->getMessage());

                        Log::error('Newsletter send failed', [
                            'newsletter_id' => $this->newsletter->id,
                            'send_id' => $send->id,
                            'subscriber_email' => $send->subscription->email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Pause between batches (except for the last one)
                if ($index < $pendingSends->count() / $batchSize - 1) {
                    sleep($batchDelay);
                }
            }

            // Mark newsletter as sent
            $this->newsletter->update([
                'status' => 'sent',
                'sent_at' => now(),
                'finished_sending_at' => now(),
            ]);

            Log::info('Newsletter sent successfully', [
                'newsletter_id' => $this->newsletter->id,
                'subject' => $this->newsletter->subject,
                'total_sent' => $this->newsletter->sent_count,
                'total_failed' => $this->newsletter->failed_count,
            ]);

        } catch (\Exception $e) {
            // Revert newsletter status to draft on failure
            $this->newsletter->update([
                'status' => 'draft',
                'started_sending_at' => null,
            ]);

            Log::error('Newsletter job failed', [
                'newsletter_id' => $this->newsletter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Revert newsletter status
        $this->newsletter->update([
            'status' => 'draft',
            'started_sending_at' => null,
        ]);

        Log::error('Newsletter job permanently failed', [
            'newsletter_id' => $this->newsletter->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
