<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_displays_results(): void
    {
        Product::factory()->create([
            'name' => 'Classic Logo T-Shirt',
            'status' => 'active',
        ]);

        Product::factory()->create([
            'name' => 'Vintage Poster Print',
            'status' => 'active',
        ]);

        $response = $this->get(route('search', ['q' => 'Classic']));

        $response->assertOk();
        $response->assertSee('Classic Logo T-Shirt');
        $response->assertDontSee('Vintage Poster Print');
    }

    public function test_search_filters_by_type(): void
    {
        Product::factory()->create([
            'name' => 'Retro Graphic Hoodie',
            'status' => 'active',
        ]);

        Product::factory()->create([
            'name' => 'Retro Logo Mug',
            'status' => 'active',
        ]);

        $response = $this->get(route('search', ['q' => 'Retro', 'type' => 'products']));

        $response->assertOk();
        $response->assertSee('Retro Graphic Hoodie');
    }

    public function test_autocomplete_returns_json(): void
    {
        Product::factory()->create([
            'name' => 'Mountain Landscape Poster',
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/search/autocomplete?q=Mountain');

        $response->assertOk();
        $response->assertJsonStructure([
            'products' => [['id', 'name', 'slug', 'price', 'url', 'type']],
            'blog',
        ]);

        $response->assertJsonFragment(['name' => 'Mountain Landscape Poster']);
    }

    public function test_search_requires_minimum_query_length(): void
    {
        $response = $this->get(route('search', ['q' => 'a']));

        $response->assertSessionHasErrors('q');
    }

    public function test_search_only_finds_active_items(): void
    {
        Product::factory()->create([
            'name' => 'Active Graphic Tee',
            'status' => 'active',
        ]);

        Product::factory()->create([
            'name' => 'Inactive Graphic Tee',
            'status' => 'inactive',
        ]);

        $response = $this->get(route('search', ['q' => 'Graphic Tee']));

        $response->assertOk();
        $response->assertSee('Active Graphic Tee');
        $response->assertDontSee('Inactive Graphic Tee');
    }
}
