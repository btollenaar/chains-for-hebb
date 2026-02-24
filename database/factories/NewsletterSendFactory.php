<?php

namespace Database\Factories;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterSend>
 */
class NewsletterSendFactory extends Factory
{
    protected $model = NewsletterSend::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'newsletter_id' => Newsletter::factory(),
            'newsletter_subscription_id' => NewsletterSubscription::factory(),
            'tracking_token' => Str::random(64),
            'status' => 'pending',
            'sent_at' => null,
            'opened_at' => null,
            'clicked_at' => null,
            'error_message' => null,
        ];
    }

    /**
     * Indicate that the send is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'sent_at' => null,
            'opened_at' => null,
            'clicked_at' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the send was successful.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now()->subDays(fake()->numberBetween(1, 7)),
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the email was opened.
     */
    public function opened(): static
    {
        return $this->state(function (array $attributes) {
            $sentAt = $attributes['sent_at'] ?? now()->subDays(fake()->numberBetween(1, 7));

            return [
                'status' => 'sent',
                'sent_at' => $sentAt,
                'opened_at' => fake()->dateTimeBetween($sentAt, 'now'),
                'error_message' => null,
            ];
        });
    }

    /**
     * Indicate that a link in the email was clicked.
     */
    public function clicked(): static
    {
        return $this->state(function (array $attributes) {
            $sentAt = $attributes['sent_at'] ?? now()->subDays(fake()->numberBetween(1, 7));
            $openedAt = $attributes['opened_at'] ?? fake()->dateTimeBetween($sentAt, 'now');

            return [
                'status' => 'sent',
                'sent_at' => $sentAt,
                'opened_at' => $openedAt,
                'clicked_at' => fake()->dateTimeBetween($openedAt, 'now'),
                'error_message' => null,
            ];
        });
    }

    /**
     * Indicate that the send failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'sent_at' => null,
            'opened_at' => null,
            'clicked_at' => null,
            'error_message' => fake()->randomElement([
                'Connection timed out',
                'Invalid email address',
                'Mailbox full',
                'Domain not found',
                'SMTP error: 550 User unknown',
            ]),
        ]);
    }

    /**
     * Associate with a specific newsletter.
     */
    public function forNewsletter(Newsletter $newsletter): static
    {
        return $this->state(fn (array $attributes) => [
            'newsletter_id' => $newsletter->id,
        ]);
    }

    /**
     * Associate with a specific subscription.
     */
    public function forSubscription(NewsletterSubscription $subscription): static
    {
        return $this->state(fn (array $attributes) => [
            'newsletter_subscription_id' => $subscription->id,
        ]);
    }
}
