<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['profile', 'contact', 'social', 'branding', 'features', 'hours', 'theme']),
            'key' => fake()->unique()->word(),
            'value' => fake()->word(),
            'type' => 'text',
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(1, 100),
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the setting is a boolean type.
     */
    public function boolean(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'boolean',
            'value' => fake()->boolean() ? '1' : '0',
        ]);
    }

    /**
     * Indicate that the setting is a color type.
     */
    public function color(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'color',
            'value' => fake()->hexColor(),
        ]);
    }

    /**
     * Indicate that the setting is an image type.
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
            'value' => 'settings/' . fake()->uuid() . '.jpg',
            'metadata' => json_encode([
                'original_name' => fake()->word() . '.jpg',
                'size' => fake()->numberBetween(10000, 500000),
                'mime_type' => 'image/jpeg',
                'dimensions' => '800x600',
            ]),
        ]);
    }

    /**
     * Indicate that the setting is a JSON type.
     */
    public function json(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'json',
            'value' => json_encode(['key' => 'value']),
        ]);
    }

    /**
     * Set a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    /**
     * Set a specific key.
     */
    public function key(string $key): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => $key,
        ]);
    }

    /**
     * Set a specific value.
     */
    public function value(string $value): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $value,
        ]);
    }
}
