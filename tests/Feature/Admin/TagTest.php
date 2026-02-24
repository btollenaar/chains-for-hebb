<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->customer = Customer::factory()->create([
            'role' => 'customer',
        ]);
    }

    public function test_admin_can_view_tags_index(): void
    {
        Tag::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.tags.index'));

        $response->assertOk();
        $response->assertViewIs('admin.tags.index');
        $response->assertViewHas('tags');
        $response->assertViewHas('stats');
    }

    public function test_admin_can_create_tag(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.tags.store'), [
            'name' => 'Premium Buyer',
            'color' => '#FF5733',
            'description' => 'Customers who buy premium products.',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tags', [
            'name' => 'Premium Buyer',
            'slug' => 'premium-buyer',
            'color' => '#FF5733',
        ]);
    }

    public function test_admin_can_update_tag(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Old Name',
            'color' => '#000000',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.tags.update', $tag), [
            'name' => 'New Name',
            'color' => '#FFFFFF',
            'description' => 'Updated description.',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'New Name',
            'color' => '#FFFFFF',
            'description' => 'Updated description.',
        ]);
    }

    public function test_admin_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();

        // Assign tag to a customer to verify cascade cleanup
        $this->admin->tags()->attach($tag->id, ['assigned_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('customer_tag', ['tag_id' => $tag->id]);
    }

    public function test_admin_can_assign_tag_to_customer(): void
    {
        $tag = Tag::factory()->create();
        $targetCustomer = Customer::factory()->create();

        // Assign tag
        $response = $this->actingAs($this->admin)->postJson(route('admin.tags.assign'), [
            'customer_id' => $targetCustomer->id,
            'tag_id' => $tag->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'action' => 'assigned']);

        $this->assertDatabaseHas('customer_tag', [
            'customer_id' => $targetCustomer->id,
            'tag_id' => $tag->id,
            'assigned_by' => $this->admin->id,
        ]);

        // Toggle off (remove tag)
        $response = $this->actingAs($this->admin)->postJson(route('admin.tags.assign'), [
            'customer_id' => $targetCustomer->id,
            'tag_id' => $tag->id,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'action' => 'removed']);

        $this->assertDatabaseMissing('customer_tag', [
            'customer_id' => $targetCustomer->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_non_admin_cannot_access_tags(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.tags.index'));
        $response->assertForbidden();

        $response = $this->actingAs($this->customer)->post(route('admin.tags.store'), [
            'name' => 'Hacker Tag',
            'color' => '#000000',
        ]);
        $response->assertForbidden();

        // Unauthenticated user should be redirected to login
        auth()->logout();
        $response = $this->get(route('admin.tags.index'));
        $response->assertRedirect(route('login'));
    }
}
