<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Product page contains Open Graph meta tags
     * Verifies og:title, og:description, and og:type=product are present
     */
    public function test_product_page_contains_og_tags(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Premium Cotton T-Shirt',
            'description' => '<p>A comfortable cotton t-shirt for everyday wear.</p>',
            'status' => 'active',
            'stock_quantity' => 50,
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act
        $response = $this->get(route('products.show', $product->slug));

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('og:title', $content);
        $this->assertStringContainsString('og:description', $content);
        $this->assertMatchesRegularExpression(
            '/<meta\s+property="og:type"\s+content="product"\s*\/?>/',
            $content,
            'Product page should have og:type set to "product"'
        );
        $this->assertStringContainsString('Premium Cotton T-Shirt', $content);
    }

    /**
     * Test 2: Product page contains JSON-LD structured data with @type=Product
     * Verifies schema.org Product markup is present in the page
     */
    public function test_product_page_contains_json_ld_structured_data(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Vintage Graphic Hoodie',
            'description' => '<p>A retro-inspired hoodie with bold graphics.</p>',
            'sku' => 'VGH-001',
            'price' => 49.99,
            'status' => 'active',
            'stock_quantity' => 25,
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act
        $response = $this->get(route('products.show', $product->slug));

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        // Verify JSON-LD script tag is present
        $this->assertStringContainsString('application/ld+json', $content);

        // Extract and decode the JSON-LD block
        preg_match('/<script\s+type="application\/ld\+json">\s*(.*?)\s*<\/script>/s', $content, $matches);
        $this->assertNotEmpty($matches, 'Page should contain a JSON-LD script block');

        $jsonLd = json_decode($matches[1], true);
        $this->assertNotNull($jsonLd, 'JSON-LD should be valid JSON');

        // Verify structured data fields
        $this->assertEquals('https://schema.org', $jsonLd['@context']);
        $this->assertEquals('Product', $jsonLd['@type']);
        $this->assertEquals('Vintage Graphic Hoodie', $jsonLd['name']);
        $this->assertEquals('VGH-001', $jsonLd['sku']);
        $this->assertArrayHasKey('offers', $jsonLd);
        $this->assertEquals('USD', $jsonLd['offers']['priceCurrency']);
        $this->assertStringContainsString('schema.org/InStock', $jsonLd['offers']['availability']);
    }

    /**
     * Test 3: Blog post page contains OG tags with og:type=article
     * Verifies article-specific Open Graph metadata
     */
    public function test_blog_post_page_contains_og_tags_with_article_type(): void
    {
        // Arrange
        $blogCategory = BlogCategory::factory()->create();
        $author = Customer::factory()->create(['role' => 'admin']);
        $post = BlogPost::factory()->published()->create([
            'title' => 'How to Care for Your Custom Prints',
            'excerpt' => 'Learn the best practices for maintaining your printed merchandise.',
            'content' => '<p>Taking care of printed merchandise is important for longevity.</p>',
            'category_id' => $blogCategory->id,
            'author_id' => $author->id,
        ]);

        // Act
        $response = $this->get(route('blog.show', $post->slug));

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/<meta\s+property="og:type"\s+content="article"\s*\/?>/',
            $content,
            'Blog post page should have og:type set to "article"'
        );
        $this->assertStringContainsString('og:title', $content);
        $this->assertStringContainsString('og:description', $content);
        $this->assertStringContainsString('How to Care for Your Custom Prints', $content);
    }

    /**
     * Test 4: Sitemap XML is valid and returns correct content-type
     * Verifies the sitemap endpoint returns well-formed XML
     */
    public function test_sitemap_returns_valid_xml_with_correct_content_type(): void
    {
        // Arrange: create a product so the sitemap has content
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'status' => 'active',
            'stock_quantity' => 10,
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act
        $response = $this->get('/sitemap.xml');

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');

        $content = $response->getContent();

        // Verify XML structure
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString('</urlset>', $content);
        $this->assertStringContainsString('<loc>', $content);
        $this->assertStringContainsString('<lastmod>', $content);
        $this->assertStringContainsString('<changefreq>', $content);
        $this->assertStringContainsString('<priority>', $content);
    }

    /**
     * Test 5: Sitemap contains product URLs
     * Verifies that active products appear in the sitemap
     */
    public function test_sitemap_contains_product_urls(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $activeProduct = Product::factory()->create([
            'name' => 'Sitemap Test Tee',
            'status' => 'active',
            'stock_quantity' => 30,
        ]);
        $activeProduct->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        $inactiveProduct = Product::factory()->create([
            'name' => 'Hidden Draft Product',
            'status' => 'inactive',
            'stock_quantity' => 10,
        ]);

        // Act
        $response = $this->get('/sitemap.xml');

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        // Active product URL should be in sitemap
        $expectedUrl = route('products.show', $activeProduct->slug);
        $this->assertStringContainsString(htmlspecialchars($expectedUrl), $content);

        // Inactive product URL should not be in sitemap
        $excludedUrl = route('products.show', $inactiveProduct->slug);
        $this->assertStringNotContainsString(htmlspecialchars($excludedUrl), $content);
    }

    /**
     * Test 6: Pages contain canonical URL meta tag
     * Verifies rel="canonical" is present on product and home pages
     */
    public function test_pages_contain_canonical_url_meta_tag(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Canonical Test Product',
            'status' => 'active',
            'stock_quantity' => 15,
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act: Test product page canonical URL
        $response = $this->get(route('products.show', $product->slug));

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        $expectedCanonical = route('products.show', $product->slug);
        $this->assertMatchesRegularExpression(
            '/<link\s+rel="canonical"\s+href="[^"]*' . preg_quote($product->slug, '/') . '[^"]*"\s*\/?>/',
            $content,
            'Product page should contain a canonical URL with the product slug'
        );

        // Act: Test home page canonical URL
        $homeResponse = $this->get(route('home'));

        // Assert
        $homeResponse->assertStatus(200);
        $homeContent = $homeResponse->getContent();

        $this->assertMatchesRegularExpression(
            '/<link\s+rel="canonical"\s+href="[^"]*"\s*\/?>/',
            $homeContent,
            'Home page should contain a canonical URL meta tag'
        );
    }

    /**
     * Test 7: Pages contain Twitter card meta tag
     * Verifies twitter:card meta tag is present in the HTML head
     */
    public function test_pages_contain_twitter_card_meta_tag(): void
    {
        // Arrange
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Twitter Card Test Product',
            'status' => 'active',
            'stock_quantity' => 20,
        ]);
        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);

        // Act: Test product page
        $response = $this->get(route('products.show', $product->slug));

        // Assert
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/<meta\s+name="twitter:card"\s+content="summary_large_image"\s*\/?>/',
            $content,
            'Product page should contain twitter:card meta tag with summary_large_image'
        );
        $this->assertStringContainsString('twitter:title', $content);
        $this->assertStringContainsString('twitter:description', $content);

        // Act: Test home page
        $homeResponse = $this->get(route('home'));

        // Assert
        $homeResponse->assertStatus(200);
        $homeContent = $homeResponse->getContent();

        $this->assertMatchesRegularExpression(
            '/<meta\s+name="twitter:card"\s+content="summary_large_image"\s*\/?>/',
            $homeContent,
            'Home page should contain twitter:card meta tag with summary_large_image'
        );
    }
}
