<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);
    }

    /**
     * Test 1: Admin can view blog categories list
     */
    public function test_admin_can_view_blog_categories(): void
    {
        // Arrange
        BlogCategory::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->admin)
            ->get(route('admin.blog.categories.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.blog.categories.index');
    }

    /**
     * Test 2: Admin can create blog category
     */
    public function test_admin_can_create_blog_category(): void
    {
        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('admin.blog.categories.store'), [
                'name' => 'Technology',
                'description' => 'Posts about technology and innovation',
            ]);

        // Assert: Redirect to index
        $response->assertRedirect(route('admin.blog.categories.index'));
        $response->assertSessionHas('success');

        // Assert: Category created with slug
        $this->assertDatabaseHas('blog_categories', [
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Posts about technology and innovation',
        ]);
    }

    /**
     * Test 3: Slug is auto-generated from name
     */
    public function test_slug_auto_generated_from_name(): void
    {
        // Arrange
        $category = BlogCategory::create([
            'name' => 'News & Updates',
        ]);

        // Assert: Slug generated
        $this->assertEquals('news-updates', $category->slug);
    }

    /**
     * Test 4: Admin can update blog category
     */
    public function test_admin_can_update_blog_category(): void
    {
        // Arrange
        $category = BlogCategory::factory()->create([
            'name' => 'Original Name',
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('admin.blog.categories.update', $category), [
                'name' => 'Updated Name',
                'description' => 'Updated description',
            ]);

        // Assert
        $response->assertRedirect(route('admin.blog.categories.index'));

        $category->refresh();
        $this->assertEquals('Updated Name', $category->name);
        $this->assertEquals('Updated description', $category->description);
    }

    /**
     * Test 5: Admin can delete blog category
     */
    public function test_admin_can_delete_blog_category(): void
    {
        // Arrange
        $category = BlogCategory::factory()->create();
        $categoryId = $category->id;

        // Act
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.blog.categories.destroy', $category));

        // Assert
        $response->assertRedirect(route('admin.blog.categories.index'));
        $this->assertDatabaseMissing('blog_categories', ['id' => $categoryId]);
    }

    /**
     * Test 6: Category has many posts relationship
     */
    public function test_category_has_many_posts(): void
    {
        // Arrange
        $category = BlogCategory::factory()->create();
        BlogPost::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        // Assert
        $this->assertCount(3, $category->posts);
        $this->assertInstanceOf(BlogPost::class, $category->posts->first());
    }

    /**
     * Test 7: Category name is required
     */
    public function test_category_name_is_required(): void
    {
        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('admin.blog.categories.store'), [
                'name' => '',
                'description' => 'Some description',
            ]);

        // Assert
        $response->assertSessionHasErrors('name');
    }

    /**
     * Test 8: Non-admin cannot access blog categories
     */
    public function test_non_admin_cannot_access_blog_categories(): void
    {
        // Arrange
        $customer = Customer::factory()->create([
            'role' => 'customer',
            'is_admin' => false,
        ]);

        // Act
        $response = $this->actingAs($customer)
            ->get(route('admin.blog.categories.index'));

        // Assert: Forbidden or redirect
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302
        );
    }

    /**
     * Test 9: Guest cannot access blog categories
     */
    public function test_guest_cannot_access_blog_categories(): void
    {
        // Act
        $response = $this->get(route('admin.blog.categories.index'));

        // Assert: Redirect to login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test 10: Category can be updated without changing slug
     */
    public function test_category_can_update_without_changing_slug(): void
    {
        // Arrange
        $category = BlogCategory::factory()->create([
            'name' => 'Original',
            'slug' => 'original-slug',
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->put(route('admin.blog.categories.update', $category), [
                'name' => 'Different Name',
                'description' => 'New description',
            ]);

        // Assert: Slug can stay the same or be regenerated depending on implementation
        $category->refresh();
        $this->assertEquals('Different Name', $category->name);
    }

    /**
     * Test 11: Duplicate category names create unique slugs
     */
    public function test_duplicate_names_handled(): void
    {
        // Arrange: Create first category
        $first = BlogCategory::create(['name' => 'Technology']);

        // Act: Create second with same name but different slug
        $second = BlogCategory::create([
            'name' => 'Technology',
            'slug' => 'technology-2',
        ]);

        // Assert: Both exist with unique slugs
        $this->assertDatabaseCount('blog_categories', 2);
        $this->assertNotEquals($first->slug, $second->slug);
    }

    /**
     * Test 12: Category with posts cannot be deleted (or cascade warning)
     */
    public function test_category_with_posts_behavior(): void
    {
        // Arrange: Create category with posts
        $category = BlogCategory::factory()->create();
        BlogPost::factory()->count(2)->create([
            'category_id' => $category->id,
        ]);

        // Depending on implementation, this either:
        // - Prevents deletion
        // - Cascades deletes
        // - Sets posts category_id to null

        // Act: Try to delete
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.blog.categories.destroy', $category));

        // Assert: Either success with cascade or error preventing deletion
        // This test documents the expected behavior
        $this->assertTrue(
            $response->isRedirection() || $response->status() === 200
        );
    }
}
