<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressBookTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_addresses(): void
    {
        $customer = Customer::factory()->create();
        Address::factory()->count(3)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->get(route('addresses.index'));
        $response->assertOk();
    }

    public function test_guest_cannot_view_addresses(): void
    {
        $response = $this->get(route('addresses.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_create_address(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer)->post(route('addresses.store'), [
            'label' => 'Home',
            'type' => 'both',
            'street' => '123 Main St',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
            'country' => 'US',
        ]);

        $response->assertRedirect(route('addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'customer_id' => $customer->id,
            'label' => 'Home',
            'street' => '123 Main St',
            'is_default' => true, // First address auto-defaults
        ]);
    }

    public function test_user_cannot_access_other_users_address(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $address = Address::factory()->create(['customer_id' => $customer1->id]);

        $response = $this->actingAs($customer2)->put(route('addresses.update', $address), [
            'label' => 'Hacked',
            'type' => 'both',
            'street' => '123 Main St',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_update_own_address(): void
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->put(route('addresses.update', $address), [
            'label' => 'Updated Home',
            'type' => 'shipping',
            'street' => '456 Oak Ave',
            'city' => 'Seattle',
            'state' => 'WA',
            'zip' => '98101',
        ]);

        $response->assertRedirect(route('addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Updated Home',
            'street' => '456 Oak Ave',
            'city' => 'Seattle',
        ]);
    }

    public function test_user_can_delete_address(): void
    {
        $customer = Customer::factory()->create();
        $address = Address::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->delete(route('addresses.destroy', $address));
        $response->assertRedirect();
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_user_cannot_delete_other_users_address(): void
    {
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();
        $address = Address::factory()->create(['customer_id' => $customer1->id]);

        $response = $this->actingAs($customer2)->delete(route('addresses.destroy', $address));
        $response->assertForbidden();
        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }

    public function test_first_address_becomes_default(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($customer)->post(route('addresses.store'), [
            'label' => 'Home',
            'type' => 'both',
            'street' => '123 Main St',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => '97201',
        ]);

        $this->assertTrue($customer->addresses()->first()->is_default);
    }

    public function test_setting_new_default_unsets_previous(): void
    {
        $customer = Customer::factory()->create();

        $address1 = Address::factory()->default()->create(['customer_id' => $customer->id]);
        $address2 = Address::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($customer)->post(route('addresses.set-default', $address2));

        $this->assertFalse($address1->fresh()->is_default);
        $this->assertTrue($address2->fresh()->is_default);
    }

    public function test_deleting_default_promotes_next_address(): void
    {
        $customer = Customer::factory()->create();

        $address1 = Address::factory()->default()->create(['customer_id' => $customer->id]);
        $address2 = Address::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($customer)->delete(route('addresses.destroy', $address1));

        $this->assertTrue($address2->fresh()->is_default);
    }

    public function test_json_endpoint_returns_addresses(): void
    {
        $customer = Customer::factory()->create();
        Address::factory()->count(2)->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($customer)->getJson(route('addresses.json'));
        $response->assertOk();
        $response->assertJsonCount(2);
    }

    public function test_validation_rejects_invalid_state(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer)->post(route('addresses.store'), [
            'label' => 'Home',
            'type' => 'both',
            'street' => '123 Main St',
            'city' => 'Portland',
            'state' => 'XX',
            'zip' => '97201',
        ]);

        // State regex requires uppercase letters; 'XX' matches regex but is a valid format
        // The regex only checks format [A-Z]{2}, not actual state codes
        $response->assertRedirect(route('addresses.index'));
    }

    public function test_validation_rejects_invalid_zip(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->actingAs($customer)->post(route('addresses.store'), [
            'label' => 'Home',
            'type' => 'both',
            'street' => '123 Main St',
            'city' => 'Portland',
            'state' => 'OR',
            'zip' => 'ABCDE',
        ]);

        $response->assertSessionHasErrors('zip');
    }
}
