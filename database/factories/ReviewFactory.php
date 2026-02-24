<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'reviewable_type' => Product::class,  // Default to Product
            'reviewable_id' => Product::factory(),
            'rating' => fake()->numberBetween(3, 5),  // Most reviews are positive
            'title' => fake()->sentence(5),
            'comment' => fake()->paragraph(),
            'verified_purchase' => fake()->boolean(60),  // 60% are verified
            'status' => 'pending',
            'helpful_count' => 0,
            'not_helpful_count' => 0,
            'admin_response' => null,
            'responded_at' => null,
        ];
    }

    /**
     * Indicate that the review is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the review is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the review is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the purchase is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_purchase' => true,
        ]);
    }

    /**
     * Indicate that the review is for a product.
     */
    public function forProduct(Product|int|null $product = null): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $productInstance = $product instanceof Product
                ? $product
                : Product::factory()->create();

            return [
                'reviewable_type' => Product::class,
                'reviewable_id' => $productInstance->id,
            ];
        });
    }

    /**
     * Indicate that the review is for a service.
     */
    public function forService(Service|int|null $service = null): static
    {
        return $this->state(function (array $attributes) use ($service) {
            $serviceInstance = $service instanceof Service
                ? $service
                : Service::factory()->create();

            return [
                'reviewable_type' => Service::class,
                'reviewable_id' => $serviceInstance->id,
            ];
        });
    }

    /**
     * Set a specific rating (1-5 stars).
     */
    public function rating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => max(1, min(5, $rating)),  // Clamp between 1-5
        ]);
    }

    /**
     * Add an admin response to the review.
     */
    public function withAdminResponse(string $response = null): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_response' => $response ?? fake()->paragraph(),
            'responded_at' => now()->subDays(fake()->numberBetween(1, 10)),
        ]);
    }

    /**
     * Add helpful votes to the review.
     */
    public function helpful(int $count = null): static
    {
        return $this->state(fn (array $attributes) => [
            'helpful_count' => $count ?? fake()->numberBetween(5, 50),
        ]);
    }

    /**
     * Create a negative review (1-2 stars).
     */
    public function negative(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(1, 2),
            'title' => fake()->randomElement([
                'Disappointed',
                'Not what I expected',
                'Could be better',
                'Issues with product',
            ]),
        ]);
    }

    /**
     * Create a positive review (4-5 stars).
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => fake()->numberBetween(4, 5),
            'title' => fake()->randomElement([
                'Excellent!',
                'Highly recommend',
                'Great experience',
                'Love it!',
            ]),
        ]);
    }
}
