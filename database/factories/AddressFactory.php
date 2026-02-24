<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = ['AL','AK','AZ','AR','CA','CO','CT','DE','FL','GA','HI','ID','IL','IN','IA','KS','KY','LA','ME','MD','MA','MI','MN','MS','MO','MT','NE','NV','NH','NJ','NM','NY','NC','ND','OH','OK','OR','PA','RI','SC','SD','TN','TX','UT','VT','VA','WA','WV','WI','WY'];

        return [
            'customer_id' => Customer::factory(),
            'label' => fake()->randomElement(['Home', 'Work', 'Office', 'Parents', 'Vacation']),
            'type' => fake()->randomElement(['shipping', 'billing', 'both']),
            'is_default' => false,
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->randomElement($states),
            'zip' => fake()->numerify('#####'),
            'country' => 'US',
            'phone' => fake()->optional()->numerify('(###) ###-####'),
        ];
    }

    /**
     * Mark the address as default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    /**
     * Set the address type to shipping only.
     */
    public function shipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'shipping',
        ]);
    }

    /**
     * Set the address type to billing only.
     */
    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'billing',
        ]);
    }

    /**
     * Set the address type to both shipping and billing.
     */
    public function both(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'both',
        ]);
    }
}
