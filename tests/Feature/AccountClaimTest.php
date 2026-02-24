<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AccountClaimTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Claim page shows for guest customer without password
     */
    public function test_claim_page_shows_for_guest_customer(): void
    {
        // Arrange: Create customer without password (guest checkout)
        $customer = Customer::factory()->create([
            'password' => null,
            'email' => 'guest@example.com',
        ]);

        // Act: Visit claim page with signed URL
        $response = $this->get(URL::signedRoute('account.claim.show', $customer));

        // Assert: Page loads successfully
        $response->assertStatus(200);
        $response->assertViewIs('account.claim');
        $response->assertViewHas('customer', $customer);
    }

    /**
     * Test 2: Claim page redirects if customer already has password
     */
    public function test_claim_page_redirects_for_existing_account(): void
    {
        // Arrange: Create customer with password
        $customer = Customer::factory()->create([
            'password' => Hash::make('existingpassword'),
        ]);

        // Act: Visit claim page with signed URL
        $response = $this->get(URL::signedRoute('account.claim.show', $customer));

        // Assert: Redirected to login
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');
    }

    /**
     * Test 3: Guest can claim account by setting password
     */
    public function test_guest_can_claim_account(): void
    {
        // Arrange: Create guest customer
        $customer = Customer::factory()->create([
            'password' => null,
            'email' => 'newcustomer@example.com',
        ]);

        // Act: Submit password with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        // Assert: Redirected to dashboard
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        // Assert: Password was set
        $customer->refresh();
        $this->assertTrue(Hash::check('SecurePassword123!', $customer->password));

        // Assert: User is logged in
        $this->assertAuthenticatedAs($customer);
    }

    /**
     * Test 4: Cannot claim already claimed account
     */
    public function test_cannot_claim_already_claimed_account(): void
    {
        // Arrange: Create customer with password
        $customer = Customer::factory()->create([
            'password' => Hash::make('existingpassword'),
        ]);

        // Act: Try to set new password with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        // Assert: Redirected to login with info message
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');

        // Assert: Password unchanged
        $customer->refresh();
        $this->assertTrue(Hash::check('existingpassword', $customer->password));
    }

    /**
     * Test 5: Password validation requires confirmation
     */
    public function test_password_requires_confirmation(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['password' => null]);

        // Act: Submit without confirmation with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'SecurePassword123!',
        ]);

        // Assert: Validation error
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test 6: Password validation requires matching confirmation
     */
    public function test_password_confirmation_must_match(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['password' => null]);

        // Act: Submit with mismatched confirmation with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'DifferentPassword456!',
        ]);

        // Assert: Validation error
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test 7: Password is required
     */
    public function test_password_is_required(): void
    {
        // Arrange
        $customer = Customer::factory()->create(['password' => null]);

        // Act: Submit without password with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => '',
            'password_confirmation' => '',
        ]);

        // Assert: Validation error
        $response->assertSessionHasErrors('password');
    }

    /**
     * Test 8: User is automatically logged in after claiming
     */
    public function test_user_logged_in_after_claiming(): void
    {
        // Arrange
        $customer = Customer::factory()->create([
            'password' => null,
            'email' => 'autologin@example.com',
        ]);

        // Assert: Not logged in before
        $this->assertGuest();

        // Act: Claim account with signed URL
        $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
        ]);

        // Assert: Now logged in as customer
        $this->assertAuthenticatedAs($customer);
    }

    /**
     * Test 9: Claim works for guest checkout scenario
     */
    public function test_claim_works_for_guest_checkout_customer(): void
    {
        // Arrange: Create customer as would happen during guest checkout (using factory)
        $customer = Customer::factory()->create([
            'name' => 'Guest Buyer',
            'email' => 'guestbuyer@example.com',
            'password' => null,
            'phone' => '555-123-4567',
        ]);

        // Act: Claim account with signed URL
        $response = $this->post(URL::signedRoute('account.claim.store', $customer), [
            'password' => 'GuestPassword123!',
            'password_confirmation' => 'GuestPassword123!',
        ]);

        // Assert: Success
        $response->assertRedirect(route('dashboard'));

        // Assert: Customer can now log in
        $this->assertAuthenticatedAs($customer);
        $customer->refresh();
        $this->assertNotNull($customer->password);
    }
}
