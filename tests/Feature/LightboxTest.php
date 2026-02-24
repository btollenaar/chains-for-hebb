<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LightboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_has_lightbox_attributes_with_images(): void
    {
        $product = Product::factory()->create([
            'images' => ['products/test1.jpg', 'products/test2.jpg'],
        ]);

        $response = $this->get(route('products.show', $product->slug));
        $response->assertOk();
        $response->assertSee('data-glightbox', false);
        $response->assertSee('gallery:product', false);
        $response->assertSee('gallery-image-wrapper', false);
    }

    public function test_product_page_without_images_has_no_lightbox(): void
    {
        $product = Product::factory()->create([
            'images' => [],
        ]);

        $response = $this->get(route('products.show', $product->slug));
        $response->assertOk();
        $response->assertDontSee('data-glightbox', false);
    }

}
