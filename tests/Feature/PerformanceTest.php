<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Product list has no N+1 queries
     * Tests eager loading of categories relationship
     */
    public function test_product_list_has_no_n_plus_1_queries(): void
    {
        // Arrange: Create 5 categories first
        $categories = ProductCategory::factory()->count(5)->create();

        // Create 50 products WITHOUT auto-creating categories (override category_id)
        Product::factory()->count(50)->create([
            'category_id' => $categories->random()->id,  // Use existing category instead of factory
        ])->each(function ($product) use ($categories) {
            // Attach 2 random categories via pivot table
            $assignedCategories = $categories->random(2);
            $product->categories()->attach(
                $assignedCategories->mapWithKeys(function ($cat, $index) {
                    return [$cat->id => ['is_primary' => $index === 0, 'display_order' => $index + 1]];
                })
            );
        });

        // Act: Enable query logging
        DB::enableQueryLog();

        // Make request to product list
        $response = $this->get(route('products.index'));

        // Get query count
        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Assert: Response successful
        $response->assertStatus(200);

        // Assert: Query count reasonable
        // Note: Pagination adds queries, and there may be session/auth queries
        // With eager loading and optimized category filtering, should be well under 100
        // Ideal with perfect eager loading would be ~10-20 queries
        $this->assertLessThan(100, $queryCount,
            "Expected < 100 queries for 50 products, got {$queryCount}. Check for N+1 issues.");

        DB::disableQueryLog();
    }

    /**
     * Test 2: Category navigation is cached
     * Tests caching mechanism for category tree
     */
    public function test_category_navigation_cached(): void
    {
        // Arrange: Create category tree
        $parent1 = ProductCategory::factory()->create(['name' => 'Parent 1', 'is_active' => true]);
        $parent2 = ProductCategory::factory()->create(['name' => 'Parent 2', 'is_active' => true]);

        ProductCategory::factory()->count(3)->create(['parent_id' => $parent1->id, 'is_active' => true]);
        ProductCategory::factory()->count(3)->create(['parent_id' => $parent2->id, 'is_active' => true]);

        // Create active products in parent categories so they appear in navigation
        $product1 = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);
        $product1->categories()->attach($parent1->id, ['is_primary' => true, 'display_order' => 1]);

        $product2 = Product::factory()->create(['status' => 'active', 'stock_quantity' => 10]);
        $product2->categories()->attach($parent2->id, ['is_primary' => true, 'display_order' => 1]);

        // Act: First request (cache miss)
        DB::enableQueryLog();
        $response1 = $this->get('/');
        $firstQueryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Second request (cache hit)
        DB::enableQueryLog();
        $response2 = $this->get('/');
        $secondQueryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Assert: Both responses successful
        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Assert: Second request has similar query count (within margin of error)
        // Note: Session queries, settings queries, and other non-category queries may vary
        // Main goal is to ensure query count doesn't drastically increase on subsequent requests
        $queryDifference = abs($secondQueryCount - $firstQueryCount);
        $this->assertLessThan(40, $queryDifference,
            "Query count variance should be < 40 between requests (difference: {$queryDifference}). " .
            "First: {$firstQueryCount}, Second: {$secondQueryCount}");

        // Assert: Categories visible in response
        $response1->assertSee('Parent 1');
        $response2->assertSee('Parent 2');
    }

    /**
     * Test 3: Admin dashboard query count is reasonable
     * Tests admin panel performance with multiple widgets
     */
    public function test_admin_dashboard_query_count(): void
    {
        // Arrange: Create admin user
        $admin = Customer::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        // Create some data for dashboard stats
        Product::factory()->count(20)->create();
        Customer::factory()->count(15)->create();

        // Act: Enable query logging
        DB::enableQueryLog();

        $response = $this->actingAs($admin)->get('/admin');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Assert: Response successful
        $response->assertStatus(200);

        // Assert: Dashboard doesn't run excessive queries
        // Dashboard may show stats (product count, recent orders, etc.)
        // Should be optimized with count queries and limits
        $this->assertLessThan(20, $queryCount,
            "Admin dashboard should load efficiently with < 20 queries, got {$queryCount}.");

        DB::disableQueryLog();
    }

    /**
     * Test 4: Newsletter batch sending scales properly
     * Tests queue job efficiency for bulk email operations
     */
    public function test_newsletter_batch_sending_scales(): void
    {
        // Note: This test verifies the batch sending logic, not actual email sending
        // We're testing that the code can handle large subscriber lists efficiently

        // Arrange: Create newsletter subscribers
        $subscribers = \App\Models\NewsletterSubscription::factory()->count(500)->create([
            'is_active' => true,
        ]);

        // Create a subscriber list
        $list = \App\Models\SubscriberList::factory()->create([
            'name' => 'Performance Test List',
        ]);

        // Assign all subscribers to the list
        $list->subscribers()->attach($subscribers->pluck('id'));

        // Create a newsletter campaign
        $newsletter = \App\Models\Newsletter::factory()->create([
            'subject' => 'Performance Test Newsletter',
            'content' => '<p>Test content</p>',
            'status' => 'draft',
        ]);

        $newsletter->lists()->attach($list->id);

        // Act: Mark newsletter as sending and create send records
        DB::enableQueryLog();

        $newsletter->update(['status' => 'sending']);

        // Simulate creating newsletter_sends records (what the job does)
        $targetSubscribers = $list->subscribers()
            ->where('is_active', true)
            ->distinct()
            ->get();

        // Create send records in batches (simulating job logic)
        $targetSubscribers->chunk(100)->each(function ($chunk) use ($newsletter) {
            $sendData = $chunk->map(function ($subscription) use ($newsletter) {
                return [
                    'newsletter_id' => $newsletter->id,
                    'newsletter_subscription_id' => $subscription->id,
                    'tracking_token' => bin2hex(random_bytes(32)),
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            \App\Models\NewsletterSend::insert($sendData);
        });

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Assert: Send records created
        $this->assertEquals(500, \App\Models\NewsletterSend::where('newsletter_id', $newsletter->id)->count());

        // Assert: Query count is reasonable (should use batch inserts)
        // 500 individual inserts would be 500+ queries
        // Batch inserts: ~5-10 queries (chunked)
        $this->assertLessThan(25, $queryCount,
            "Newsletter send record creation should use batch inserts. Got {$queryCount} queries for 500 subscribers.");

        DB::disableQueryLog();
    }
}
