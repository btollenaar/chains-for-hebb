<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => BlogCategory::factory(),
            'author_id' => Customer::factory()->create(['role' => 'admin']),
            'title' => fake()->sentence(6),
            'slug' => null,  // Auto-generated in model boot
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true),
            'featured_image' => null,
            'featured_image_alt' => null,
            'published' => fake()->boolean(70),  // 70% published
            'published_at' => fake()->boolean(70) ? now()->subDays(fake()->numberBetween(1, 90)) : null,
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 90)),
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Add a featured image to the post.
     */
    public function withFeaturedImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured_image' => 'blog/post-' . fake()->uuid() . '.jpg',
            'featured_image_alt' => fake()->sentence(5),
        ]);
    }

    /**
     * Set a specific author.
     */
    public function byAuthor(Customer|int $author): static
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $author instanceof Customer ? $author->id : $author,
        ]);
    }
}
