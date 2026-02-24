<?php

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create test data
DB::table('product_product_category')->delete();
Product::truncate();
ProductCategory::truncate();

$categories = ProductCategory::factory()->count(5)->create();
Product::factory()->count(50)->create()->each(function ($product) use ($categories) {
    $assignedCategories = $categories->random(2);
    $product->categories()->attach(
        $assignedCategories->mapWithKeys(function ($cat, $index) {
            return [$cat->id => ['is_primary' => $index === 0, 'display_order' => $index + 1]];
        })
    );
});

// Enable query logging
DB::enableQueryLog();

// Simulate the request
$query = Product::query()->where('status', 'active')->where('stock_quantity', '>', 0);
$products = $query->with('categories')->orderBy('name')->paginate(12);

// Force pagination to load
$products->items();

// Get queries
$queries = DB::getQueryLog();

echo "Total queries: " . count($queries) . "\n\n";

// Group queries by type
$queryTypes = [];
foreach ($queries as $query) {
    $sql = $query['query'];

    // Categorize query
    if (str_contains($sql, 'select * from "products"')) {
        $type = 'Main product query';
    } elseif (str_contains($sql, 'select * from "product_categories"')) {
        $type = 'Category query';
    } elseif (str_contains($sql, 'select * from "product_product_category"')) {
        $type = 'Pivot query';
    } else {
        $type = 'Other: ' . substr($sql, 0, 50);
    }

    if (!isset($queryTypes[$type])) {
        $queryTypes[$type] = 0;
    }
    $queryTypes[$type]++;
}

echo "Query breakdown:\n";
foreach ($queryTypes as $type => $count) {
    echo "  {$type}: {$count}\n";
}

echo "\nFirst 10 queries:\n";
foreach (array_slice($queries, 0, 10) as $i => $query) {
    echo ($i + 1) . ". " . $query['query'] . "\n";
    echo "   Bindings: " . json_encode($query['bindings']) . "\n\n";
}
