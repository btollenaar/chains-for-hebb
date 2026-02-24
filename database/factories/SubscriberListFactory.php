<?php

namespace Database\Factories;

use App\Models\SubscriberList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriberList>
 */
class SubscriberListFactory extends Factory
{
    protected $model = SubscriberList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'is_default' => false,
            'is_system' => false,
            'subscriber_count' => 0,
        ];
    }

    /**
     * Indicate that the list is a system list.
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
        ]);
    }

    /**
     * Indicate that the list is the default list.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Set a specific subscriber count.
     */
    public function withSubscribers(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'subscriber_count' => $count,
        ]);
    }
}
