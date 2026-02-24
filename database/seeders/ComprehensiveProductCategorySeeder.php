<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ComprehensiveProductCategorySeeder extends Seeder
{
    /**
     * Seed POD-specific product categories matching Printful's catalog.
     * 4 top-level categories with 16 subcategories (20 total).
     */
    public function run(): void
    {
        $this->command->info('Creating POD product categories...');

        ProductCategory::query()->delete();

        $categoriesCreated = 0;

        // 1. APPAREL
        $apparel = ProductCategory::create([
            'name' => 'Apparel',
            'slug' => 'apparel',
            'description' => 'Custom printed clothing and wearables',
            'display_order' => 1,
            'is_active' => true,
        ]);
        $categoriesCreated++;

        $apparelChildren = [
            ['name' => 'T-Shirts', 'slug' => 't-shirts', 'description' => 'Unisex and fitted t-shirts with custom prints', 'display_order' => 1],
            ['name' => 'Tank Tops', 'slug' => 'tank-tops', 'description' => 'Sleeveless tops for warm weather', 'display_order' => 2],
            ['name' => 'Long Sleeve Shirts', 'slug' => 'long-sleeve-shirts', 'description' => 'Long sleeve tees and henleys', 'display_order' => 3],
            ['name' => 'Hoodies & Sweatshirts', 'slug' => 'hoodies-sweatshirts', 'description' => 'Pullover hoodies and crewneck sweatshirts', 'display_order' => 4],
            ['name' => 'Jackets', 'slug' => 'jackets', 'description' => 'Windbreakers and lightweight jackets', 'display_order' => 5],
            ['name' => 'All-Over Print', 'slug' => 'all-over-print', 'description' => 'Full sublimation all-over print apparel', 'display_order' => 6],
        ];

        foreach ($apparelChildren as $child) {
            ProductCategory::create(array_merge($child, [
                'parent_id' => $apparel->id,
                'is_active' => true,
            ]));
            $categoriesCreated++;
        }

        // 2. ACCESSORIES
        $accessories = ProductCategory::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Hats, bags, phone cases, and more',
            'display_order' => 2,
            'is_active' => true,
        ]);
        $categoriesCreated++;

        $accessoryChildren = [
            ['name' => 'Hats & Caps', 'slug' => 'hats-caps', 'description' => 'Embroidered and printed headwear', 'display_order' => 1],
            ['name' => 'Bags & Totes', 'slug' => 'bags-totes', 'description' => 'Canvas totes and drawstring bags', 'display_order' => 2],
            ['name' => 'Phone Cases', 'slug' => 'phone-cases', 'description' => 'Custom printed phone cases', 'display_order' => 3],
            ['name' => 'Stickers', 'slug' => 'stickers', 'description' => 'Die-cut and kiss-cut stickers', 'display_order' => 4],
        ];

        foreach ($accessoryChildren as $child) {
            ProductCategory::create(array_merge($child, [
                'parent_id' => $accessories->id,
                'is_active' => true,
            ]));
            $categoriesCreated++;
        }

        // 3. HOME & LIVING
        $home = ProductCategory::create([
            'name' => 'Home & Living',
            'slug' => 'home-living',
            'description' => 'Mugs, posters, pillows, and home decor',
            'display_order' => 3,
            'is_active' => true,
        ]);
        $categoriesCreated++;

        $homeChildren = [
            ['name' => 'Mugs & Drinkware', 'slug' => 'mugs-drinkware', 'description' => 'Ceramic mugs and tumblers', 'display_order' => 1],
            ['name' => 'Posters & Wall Art', 'slug' => 'posters-wall-art', 'description' => 'Matte and glossy posters, canvas prints', 'display_order' => 2],
            ['name' => 'Pillows', 'slug' => 'pillows', 'description' => 'Custom printed throw pillows', 'display_order' => 3],
            ['name' => 'Blankets', 'slug' => 'blankets', 'description' => 'Fleece and sherpa blankets', 'display_order' => 4],
        ];

        foreach ($homeChildren as $child) {
            ProductCategory::create(array_merge($child, [
                'parent_id' => $home->id,
                'is_active' => true,
            ]));
            $categoriesCreated++;
        }

        // 4. KIDS & BABY
        $kids = ProductCategory::create([
            'name' => 'Kids & Baby',
            'slug' => 'kids-baby',
            'description' => 'Apparel for kids and infants',
            'display_order' => 4,
            'is_active' => true,
        ]);
        $categoriesCreated++;

        $kidsChildren = [
            ['name' => "Kids' T-Shirts", 'slug' => 'kids-t-shirts', 'description' => 'Custom printed tees for children', 'display_order' => 1],
            ['name' => 'Baby Onesies', 'slug' => 'baby-onesies', 'description' => 'Infant bodysuits and onesies', 'display_order' => 2],
        ];

        foreach ($kidsChildren as $child) {
            ProductCategory::create(array_merge($child, [
                'parent_id' => $kids->id,
                'is_active' => true,
            ]));
            $categoriesCreated++;
        }

        $this->command->info("  Created {$categoriesCreated} POD product categories (4 top-level + 16 subcategories)");
    }
}
