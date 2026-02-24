<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Newsletter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Newsletter>
 */
class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = '<h1>' . fake()->sentence() . '</h1>' . fake()->paragraphs(3, true);

        return [
            'subject' => fake()->sentence(6),
            'preview_text' => fake()->sentence(10),
            'content' => $content,
            'plain_text_content' => strip_tags($content),
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
            'started_sending_at' => null,
            'finished_sending_at' => null,
            'recipient_count' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
            'open_count' => 0,
            'click_count' => 0,
            'created_by' => Customer::factory()->create(['role' => 'admin']),
            'from_name' => config('mail.from.name'),
            'from_email' => config('mail.from.address'),
        ];
    }

    /**
     * Indicate that the newsletter is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the newsletter is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 7)),
        ]);
    }

    /**
     * Indicate that the newsletter has been sent.
     */
    public function sent(): static
    {
        return $this->state(function (array $attributes) {
            $recipientCount = fake()->numberBetween(100, 1000);
            $sentCount = $recipientCount - fake()->numberBetween(0, 10);
            $openCount = fake()->numberBetween(0, $sentCount);
            $clickCount = fake()->numberBetween(0, $openCount);

            return [
                'status' => 'sent',
                'sent_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'started_sending_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'finished_sending_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'recipient_count' => $recipientCount,
                'sent_count' => $sentCount,
                'failed_count' => $recipientCount - $sentCount,
                'open_count' => $openCount,
                'click_count' => $clickCount,
            ];
        });
    }

    /**
     * Indicate that the newsletter is being sent.
     */
    public function sending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sending',
            'started_sending_at' => now()->subMinutes(fake()->numberBetween(1, 30)),
        ]);
    }
}
