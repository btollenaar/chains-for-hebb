<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMockup;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class Comprehensive100ProductSeeder extends Seeder
{
    /**
     * Seed 35 print-on-demand products with ~235 variants across POD categories.
     */
    public function run(): void
    {
        $this->command->info('Creating POD products with variants...');

        Product::query()->delete();
        ProductMockup::query()->delete();

        $categories = ProductCategory::all()->keyBy('slug');
        $productsCreated = 0;
        $variantsCreated = 0;
        $variantIdCounter = 4000; // Sequential Printful variant IDs

        // Product mockup images (Unsplash free photos)
        $mockupImages = [
            // T-Shirts
            'classic-logo-tee' => 'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=600&h=600&fit=crop&auto=format&q=80',
            'mountain-sunset-tee' => 'https://images.unsplash.com/photo-1618453292459-53424b66bb6a?w=600&h=600&fit=crop&auto=format&q=80',
            'abstract-waves-tee' => 'https://images.unsplash.com/photo-1564859228273-274232fdb516?w=600&h=600&fit=crop&auto=format&q=80',
            'portland-oregon-tee' => 'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=600&h=600&fit=crop&auto=format&q=80',
            'retro-vibes-tee' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=600&h=600&fit=crop&auto=format&q=80',
            'wildflower-womens-tee' => 'https://images.unsplash.com/photo-1622445275463-afa2ab738c34?w=600&h=600&fit=crop&auto=format&q=80',
            'pnw-badge-tee' => 'https://images.unsplash.com/photo-1651761179569-4ba2aa054997?w=600&h=600&fit=crop&auto=format&q=80',
            'minimalist-line-art-tee' => 'https://images.unsplash.com/photo-1620799139507-2a76f79a2f4d?w=600&h=600&fit=crop&auto=format&q=80',
            // Tank Tops
            'summer-vibes-tank' => 'https://images.unsplash.com/photo-1571945153237-4929e783af4a?w=600&h=600&fit=crop&auto=format&q=80',
            'gym-motivation-tank' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600&h=600&fit=crop&auto=format&q=80',
            // Long Sleeves
            'campfire-long-sleeve' => 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&h=600&fit=crop&auto=format&q=80',
            'night-sky-long-sleeve' => 'https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?w=600&h=600&fit=crop&auto=format&q=80',
            // Hoodies & Sweatshirts
            'classic-logo-hoodie' => 'https://images.unsplash.com/photo-1554411529-ee36dfde51b9?w=600&h=600&fit=crop&auto=format&q=80',
            'mountain-range-hoodie' => 'https://images.unsplash.com/photo-1609873814058-a8928924184a?w=600&h=600&fit=crop&auto=format&q=80',
            'portland-crewneck-sweatshirt' => 'https://images.unsplash.com/photo-1685328403783-00925c2a4301?w=600&h=600&fit=crop&auto=format&q=80',
            'cozy-campfire-hoodie' => 'https://images.unsplash.com/photo-1610582144787-eda2e6f293b4?w=600&h=600&fit=crop&auto=format&q=80',
            // Jackets
            'adventure-windbreaker' => 'https://images.unsplash.com/photo-1620799140188-3b2a02fd9a77?w=600&h=600&fit=crop&auto=format&q=80',
            // All-Over Print
            'tropical-pattern-aop-tee' => 'https://images.unsplash.com/photo-1503342394128-c104d54dba01?w=600&h=600&fit=crop&auto=format&q=80',
            // Hats & Caps
            'classic-dad-hat' => 'https://images.unsplash.com/photo-1606483956061-46a898dce538?w=600&h=600&fit=crop&auto=format&q=80',
            'mountain-snapback' => 'https://images.unsplash.com/photo-1556306535-0f09a537f0a3?w=600&h=600&fit=crop&auto=format&q=80',
            // Bags & Totes
            'eco-canvas-tote' => 'https://images.unsplash.com/photo-1544816155-12df9643f363?w=600&h=600&fit=crop&auto=format&q=80',
            'market-day-tote' => 'https://images.unsplash.com/photo-1591561954557-26941169b49e?w=600&h=600&fit=crop&auto=format&q=80',
            // Phone Cases
            'abstract-art-phone-case' => 'https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?w=600&h=600&fit=crop&auto=format&q=80',
            'mountain-landscape-phone-case' => 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=600&h=600&fit=crop&auto=format&q=80',
            // Stickers
            'brand-logo-sticker-pack' => 'https://images.unsplash.com/photo-1761276297637-4418549ead2d?w=600&h=600&fit=crop&auto=format&q=80',
            'nature-collection-stickers' => 'https://images.unsplash.com/photo-1572375992501-4b0892d50c69?w=600&h=600&fit=crop&auto=format&q=80',
            // Mugs & Drinkware
            'morning-brew-11oz-mug' => 'https://images.unsplash.com/photo-1687158179173-b9f3eac1fde9?w=600&h=600&fit=crop&auto=format&q=80',
            'portland-skyline-15oz-mug' => 'https://images.unsplash.com/photo-1612218884696-8b503a13f708?w=600&h=600&fit=crop&auto=format&q=80',
            'motivational-quote-11oz-mug' => 'https://images.unsplash.com/photo-1571263823814-dbba1c194a5b?w=600&h=600&fit=crop&auto=format&q=80',
            // Posters & Wall Art
            'mountain-panorama-poster' => 'https://images.unsplash.com/photo-1593959554825-e14b11e69227?w=600&h=600&fit=crop&auto=format&q=80',
            'abstract-geometric-canvas' => 'https://images.unsplash.com/photo-1604413390473-5be92e62aa97?w=600&h=600&fit=crop&auto=format&q=80',
            'pnw-map-print' => 'https://images.unsplash.com/photo-1640534936814-80670cb60f8b?w=600&h=600&fit=crop&auto=format&q=80',
            // Pillows
            'botanical-print-pillow' => 'https://images.unsplash.com/photo-1584100936595-c0654b55a2e2?w=600&h=600&fit=crop&auto=format&q=80',
            // Blankets
            'cozy-mountain-fleece-blanket' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&h=600&fit=crop&auto=format&q=80',
            // Kids
            'little-explorer-kids-tee' => 'https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?w=600&h=600&fit=crop&auto=format&q=80',
            // Baby
            'mini-adventurer-baby-onesie' => 'https://images.unsplash.com/photo-1522771930-78848d9293e8?w=600&h=600&fit=crop&auto=format&q=80',
        ];

        $products = [
            // ============================================================
            // T-SHIRTS (8 products)
            // ============================================================
            [
                'name' => 'Classic Logo Tee',
                'slug' => 'classic-logo-tee',
                'description' => 'Our signature logo on a premium cotton tee',
                'long_description' => 'The Classic Logo Tee features our iconic PrintStore logo on a premium 100% ringspun cotton t-shirt. Pre-shrunk, comfortable fit with a durable print that lasts wash after wash.',
                'sku' => 'PF-TEE-LOGO-001',
                'price' => 24.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 64.19,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'White' => '#FFFFFF', 'Navy' => '#1B2A4A', 'Heather Gray' => '#9B9B9B', 'Red' => '#C41E3A'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    8.95, 24.99
                ),
            ],
            [
                'name' => 'Mountain Sunset Tee',
                'slug' => 'mountain-sunset-tee',
                'description' => 'Vibrant mountain sunset graphic tee',
                'long_description' => 'Capture the beauty of a Pacific Northwest sunset with this stunning mountain landscape tee. Printed with eco-friendly inks on a soft cotton blend.',
                'sku' => 'PF-TEE-MTSUN-001',
                'price' => 27.99,
                'cost' => 9.50,
                'base_cost' => 9.50,
                'profit_margin' => 66.06,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Forest Green' => '#228B22', 'Slate' => '#708090'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    9.50, 27.99
                ),
            ],
            [
                'name' => 'Abstract Waves Tee',
                'slug' => 'abstract-waves-tee',
                'description' => 'Modern abstract wave design',
                'long_description' => 'A contemporary abstract wave pattern that combines art and fashion. Perfect for those who appreciate minimalist design with a creative edge.',
                'sku' => 'PF-TEE-WAVE-001',
                'price' => 24.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 64.19,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['White' => '#FFFFFF', 'Light Blue' => '#ADD8E6', 'Sand' => '#C2B280'],
                    ['S', 'M', 'L', 'XL'],
                    8.95, 24.99
                ),
            ],
            [
                'name' => 'Portland Oregon Tee',
                'slug' => 'portland-oregon-tee',
                'description' => 'Show your PDX pride',
                'long_description' => 'Rep the Rose City with this Portland, Oregon tee featuring the iconic city skyline and Mt. Hood. A must-have for locals and visitors alike.',
                'sku' => 'PF-TEE-PDX-001',
                'price' => 26.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 66.84,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Forest Green' => '#228B22', 'Heather Gray' => '#9B9B9B'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    8.95, 26.99
                ),
            ],
            [
                'name' => 'Retro Vibes Tee',
                'slug' => 'retro-vibes-tee',
                'description' => 'Throwback retro graphic design',
                'long_description' => 'Channel the good vibes of decades past with this retro-inspired graphic tee. Features a vintage color palette and groovy typography.',
                'sku' => 'PF-TEE-RETRO-001',
                'price' => 24.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 64.19,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Mustard' => '#FFDB58', 'Rust' => '#B7410E', 'Cream' => '#FFFDD0'],
                    ['S', 'M', 'L', 'XL'],
                    8.95, 24.99
                ),
            ],
            [
                'name' => 'Wildflower Women\'s Tee',
                'slug' => 'wildflower-womens-tee',
                'description' => 'Delicate wildflower illustration',
                'long_description' => 'A beautifully illustrated wildflower bouquet on a relaxed-fit women\'s tee. Soft fabric with a flattering cut.',
                'sku' => 'PF-TEE-WILD-001',
                'price' => 26.99,
                'cost' => 9.50,
                'base_cost' => 9.50,
                'profit_margin' => 64.80,
                'printful_product_id' => 457,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['White' => '#FFFFFF', 'Heather Mauve' => '#C8A2C8', 'Dusty Rose' => '#DCAE96'],
                    ['S', 'M', 'L', 'XL'],
                    9.50, 26.99
                ),
            ],
            [
                'name' => 'PNW Badge Tee',
                'slug' => 'pnw-badge-tee',
                'description' => 'Pacific Northwest badge design',
                'long_description' => 'A vintage-style badge graphic celebrating the Pacific Northwest. Features mountains, trees, and the spirit of adventure.',
                'sku' => 'PF-TEE-PNW-001',
                'price' => 24.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 64.19,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Navy' => '#1B2A4A', 'Black' => '#000000', 'Olive' => '#556B2F'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    8.95, 24.99
                ),
            ],
            [
                'name' => 'Minimalist Line Art Tee',
                'slug' => 'minimalist-line-art-tee',
                'description' => 'Clean single-line art design',
                'long_description' => 'Sophisticated single-line art on a premium tee. The minimalist mountain landscape design is perfect for those who love understated style.',
                'sku' => 'PF-TEE-LINE-001',
                'price' => 24.99,
                'cost' => 8.95,
                'base_cost' => 8.95,
                'profit_margin' => 64.19,
                'printful_product_id' => 71,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['t-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['White' => '#FFFFFF', 'Black' => '#000000'],
                    ['S', 'M', 'L', 'XL'],
                    8.95, 24.99
                ),
            ],

            // ============================================================
            // TANK TOPS (2 products)
            // ============================================================
            [
                'name' => 'Summer Vibes Tank',
                'slug' => 'summer-vibes-tank',
                'description' => 'Lightweight summer tank top',
                'long_description' => 'Stay cool all summer long with this lightweight tank featuring a sunny, tropical-inspired design. Perfect for beach days and outdoor adventures.',
                'sku' => 'PF-TANK-SUM-001',
                'price' => 22.99,
                'cost' => 7.95,
                'base_cost' => 7.95,
                'profit_margin' => 65.42,
                'printful_product_id' => 167,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['tank-tops', 'apparel'],
                'variants' => $this->expandVariants(
                    ['White' => '#FFFFFF', 'Black' => '#000000'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    7.95, 22.99
                ),
            ],
            [
                'name' => 'Gym Motivation Tank',
                'slug' => 'gym-motivation-tank',
                'description' => 'Motivational workout tank',
                'long_description' => 'Push through your workout with this motivational gym tank. Bold typography on a breathable, moisture-wicking fabric.',
                'sku' => 'PF-TANK-GYM-001',
                'price' => 22.99,
                'cost' => 7.95,
                'base_cost' => 7.95,
                'profit_margin' => 65.42,
                'printful_product_id' => 167,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['tank-tops', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Navy' => '#1B2A4A'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    7.95, 22.99
                ),
            ],

            // ============================================================
            // LONG SLEEVE SHIRTS (2 products)
            // ============================================================
            [
                'name' => 'Campfire Long Sleeve',
                'slug' => 'campfire-long-sleeve',
                'description' => 'Cozy campfire graphic long sleeve',
                'long_description' => 'Gather around the campfire with this cozy long sleeve tee featuring a hand-drawn campfire illustration. Perfect for cool evenings outdoors.',
                'sku' => 'PF-LS-CAMP-001',
                'price' => 29.99,
                'cost' => 11.50,
                'base_cost' => 11.50,
                'profit_margin' => 61.65,
                'printful_product_id' => 370,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['long-sleeve-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Forest Green' => '#228B22'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    11.50, 29.99
                ),
            ],
            [
                'name' => 'Night Sky Long Sleeve',
                'slug' => 'night-sky-long-sleeve',
                'description' => 'Starry night sky illustration',
                'long_description' => 'Gaze at the stars with this beautiful night sky illustration on a comfortable long sleeve tee. Features constellations and a crescent moon.',
                'sku' => 'PF-LS-NIGHT-001',
                'price' => 29.99,
                'cost' => 11.50,
                'base_cost' => 11.50,
                'profit_margin' => 61.65,
                'printful_product_id' => 370,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['long-sleeve-shirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Navy' => '#1B2A4A', 'Black' => '#000000'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    11.50, 29.99
                ),
            ],

            // ============================================================
            // HOODIES & SWEATSHIRTS (4 products)
            // ============================================================
            [
                'name' => 'Classic Logo Hoodie',
                'slug' => 'classic-logo-hoodie',
                'description' => 'Our signature logo on a premium hoodie',
                'long_description' => 'The Classic Logo Hoodie features our iconic PrintStore logo on a heavyweight cotton-blend pullover hoodie. Double-lined hood with matching drawstring.',
                'sku' => 'PF-HOOD-LOGO-001',
                'price' => 49.99,
                'cost' => 21.95,
                'base_cost' => 21.95,
                'profit_margin' => 56.09,
                'printful_product_id' => 146,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['hoodies-sweatshirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Navy' => '#1B2A4A', 'Heather Gray' => '#9B9B9B'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    21.95, 49.99
                ),
            ],
            [
                'name' => 'Mountain Range Hoodie',
                'slug' => 'mountain-range-hoodie',
                'description' => 'Panoramic mountain range graphic',
                'long_description' => 'A stunning panoramic mountain range graphic wraps across the chest of this cozy pullover hoodie. Inspired by the Cascade Range.',
                'sku' => 'PF-HOOD-MTN-001',
                'price' => 52.99,
                'cost' => 21.95,
                'base_cost' => 21.95,
                'profit_margin' => 58.58,
                'printful_product_id' => 146,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['hoodies-sweatshirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Forest Green' => '#228B22'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    21.95, 52.99
                ),
            ],
            [
                'name' => 'Portland Crewneck Sweatshirt',
                'slug' => 'portland-crewneck-sweatshirt',
                'description' => 'Classic Portland crewneck',
                'long_description' => 'A cozy crewneck sweatshirt with vintage Portland, Oregon lettering. Heavyweight fleece with ribbed cuffs and hem.',
                'sku' => 'PF-CREW-PDX-001',
                'price' => 44.99,
                'cost' => 19.50,
                'base_cost' => 19.50,
                'profit_margin' => 56.66,
                'printful_product_id' => 491,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['hoodies-sweatshirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Heather Gray' => '#9B9B9B', 'Black' => '#000000', 'Dusty Rose' => '#DCAE96'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    19.50, 44.99
                ),
            ],
            [
                'name' => 'Cozy Campfire Hoodie',
                'slug' => 'cozy-campfire-hoodie',
                'description' => 'Warm campfire-themed hoodie',
                'long_description' => 'Stay warm on those cool PNW nights with this campfire-themed pullover hoodie. Features a hand-illustrated campfire scene with surrounding evergreens.',
                'sku' => 'PF-HOOD-CAMP-001',
                'price' => 49.99,
                'cost' => 21.95,
                'base_cost' => 21.95,
                'profit_margin' => 56.09,
                'printful_product_id' => 146,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['hoodies-sweatshirts', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Maroon' => '#800000', 'Black' => '#000000'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    21.95, 49.99
                ),
            ],

            // ============================================================
            // JACKETS (1 product)
            // ============================================================
            [
                'name' => 'Adventure Windbreaker',
                'slug' => 'adventure-windbreaker',
                'description' => 'Lightweight windbreaker jacket',
                'long_description' => 'Hit the trails with this lightweight windbreaker featuring a custom PNW adventure graphic. Water-resistant with an adjustable hood.',
                'sku' => 'PF-JACK-ADV-001',
                'price' => 54.99,
                'cost' => 24.50,
                'base_cost' => 24.50,
                'profit_margin' => 55.45,
                'printful_product_id' => 331,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['jackets', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Black' => '#000000', 'Navy' => '#1B2A4A'],
                    ['S', 'M', 'L', 'XL'],
                    24.50, 54.99
                ),
            ],

            // ============================================================
            // ALL-OVER PRINT (1 product)
            // ============================================================
            [
                'name' => 'Tropical Pattern AOP Tee',
                'slug' => 'tropical-pattern-aop-tee',
                'description' => 'All-over print tropical pattern',
                'long_description' => 'Turn heads with this vibrant all-over print tee featuring a tropical leaf pattern. Full sublimation printing for edge-to-edge coverage.',
                'sku' => 'PF-AOP-TROP-001',
                'price' => 34.99,
                'cost' => 15.95,
                'base_cost' => 15.95,
                'profit_margin' => 54.42,
                'printful_product_id' => 88,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['all-over-print', 'apparel'],
                'variants' => $this->expandVariants(
                    ['Tropical' => '#228B22'],
                    ['S', 'M', 'L', 'XL', '2XL'],
                    15.95, 34.99
                ),
            ],

            // ============================================================
            // HATS & CAPS (2 products)
            // ============================================================
            [
                'name' => 'Classic Dad Hat',
                'slug' => 'classic-dad-hat',
                'description' => 'Embroidered logo dad hat',
                'long_description' => 'Our classic dad hat with embroidered PrintStore logo. Unstructured crown with adjustable strap for a comfortable fit.',
                'sku' => 'PF-HAT-DAD-001',
                'price' => 24.99,
                'cost' => 10.50,
                'base_cost' => 10.50,
                'profit_margin' => 57.98,
                'printful_product_id' => 206,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['hats-caps', 'accessories'],
                'variants' => [
                    ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'One Size', 'printful_cost' => 10.50, 'retail_price' => 24.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => 'One Size', 'printful_cost' => 10.50, 'retail_price' => 24.99],
                    ['color_name' => 'Navy', 'color_hex' => '#1B2A4A', 'size' => 'One Size', 'printful_cost' => 10.50, 'retail_price' => 24.99],
                ],
            ],
            [
                'name' => 'Mountain Snapback',
                'slug' => 'mountain-snapback',
                'description' => 'Mountain embroidered snapback cap',
                'long_description' => 'A structured snapback cap with embroidered mountain graphic. Flat brim with adjustable snap closure.',
                'sku' => 'PF-HAT-SNAP-001',
                'price' => 27.99,
                'cost' => 12.50,
                'base_cost' => 12.50,
                'profit_margin' => 55.34,
                'printful_product_id' => 329,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['hats-caps', 'accessories'],
                'variants' => [
                    ['color_name' => 'Black', 'color_hex' => '#000000', 'size' => 'One Size', 'printful_cost' => 12.50, 'retail_price' => 27.99],
                    ['color_name' => 'Heather Gray', 'color_hex' => '#9B9B9B', 'size' => 'One Size', 'printful_cost' => 12.50, 'retail_price' => 27.99],
                ],
            ],

            // ============================================================
            // BAGS & TOTES (2 products)
            // ============================================================
            [
                'name' => 'Eco Canvas Tote',
                'slug' => 'eco-canvas-tote',
                'description' => 'Reusable canvas tote bag',
                'long_description' => 'Carry your essentials in style with this eco-friendly canvas tote bag. Features our PrintStore logo printed with eco-friendly inks.',
                'sku' => 'PF-BAG-ECO-001',
                'price' => 19.99,
                'cost' => 7.50,
                'base_cost' => 7.50,
                'profit_margin' => 62.48,
                'printful_product_id' => 195,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['bags-totes', 'accessories'],
                'variants' => [
                    ['color_name' => 'Natural', 'color_hex' => '#FAF0E6', 'size' => 'One Size', 'printful_cost' => 7.50, 'retail_price' => 19.99],
                ],
            ],
            [
                'name' => 'Market Day Tote',
                'slug' => 'market-day-tote',
                'description' => 'Illustrated market tote bag',
                'long_description' => 'A spacious canvas tote with a charming farmers market illustration. Perfect for grocery runs, farmers markets, and everyday errands.',
                'sku' => 'PF-BAG-MKT-001',
                'price' => 19.99,
                'cost' => 7.50,
                'base_cost' => 7.50,
                'profit_margin' => 62.48,
                'printful_product_id' => 195,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['bags-totes', 'accessories'],
                'variants' => [
                    ['color_name' => 'Natural', 'color_hex' => '#FAF0E6', 'size' => 'One Size', 'printful_cost' => 7.50, 'retail_price' => 19.99],
                ],
            ],

            // ============================================================
            // PHONE CASES (2 products)
            // ============================================================
            [
                'name' => 'Abstract Art Phone Case',
                'slug' => 'abstract-art-phone-case',
                'description' => 'Colorful abstract art phone case',
                'long_description' => 'Protect your phone in style with this colorful abstract art case. Slim profile with impact-resistant material.',
                'sku' => 'PF-CASE-ABS-001',
                'price' => 19.99,
                'cost' => 7.95,
                'base_cost' => 7.95,
                'profit_margin' => 60.23,
                'printful_product_id' => 175,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['phone-cases', 'accessories'],
                'variants' => [
                    ['color_name' => 'Abstract', 'color_hex' => '#FF6B8A', 'size' => 'iPhone 14', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                    ['color_name' => 'Abstract', 'color_hex' => '#FF6B8A', 'size' => 'iPhone 15', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                    ['color_name' => 'Abstract', 'color_hex' => '#FF6B8A', 'size' => 'iPhone 15 Pro', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                ],
            ],
            [
                'name' => 'Mountain Landscape Phone Case',
                'slug' => 'mountain-landscape-phone-case',
                'description' => 'Scenic mountain phone case',
                'long_description' => 'A beautiful mountain landscape wrapping around your phone. Durable polycarbonate shell with a glossy finish.',
                'sku' => 'PF-CASE-MTN-001',
                'price' => 19.99,
                'cost' => 7.95,
                'base_cost' => 7.95,
                'profit_margin' => 60.23,
                'printful_product_id' => 175,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['phone-cases', 'accessories'],
                'variants' => [
                    ['color_name' => 'Mountain', 'color_hex' => '#4682B4', 'size' => 'iPhone 14', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                    ['color_name' => 'Mountain', 'color_hex' => '#4682B4', 'size' => 'iPhone 15', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                    ['color_name' => 'Mountain', 'color_hex' => '#4682B4', 'size' => 'iPhone 15 Pro', 'printful_cost' => 7.95, 'retail_price' => 19.99],
                ],
            ],

            // ============================================================
            // STICKERS (2 products)
            // ============================================================
            [
                'name' => 'Brand Logo Sticker Pack',
                'slug' => 'brand-logo-sticker-pack',
                'description' => 'Set of die-cut logo stickers',
                'long_description' => 'A pack of premium die-cut stickers featuring the PrintStore logo in various sizes. Waterproof vinyl with UV-resistant ink.',
                'sku' => 'PF-STICK-LOGO-001',
                'price' => 5.99,
                'cost' => 1.95,
                'base_cost' => 1.95,
                'profit_margin' => 67.45,
                'printful_product_id' => 358,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['stickers', 'accessories'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '3x3"', 'printful_cost' => 1.95, 'retail_price' => 5.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '4x4"', 'printful_cost' => 2.45, 'retail_price' => 6.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '5.5x5.5"', 'printful_cost' => 2.95, 'retail_price' => 7.99],
                ],
            ],
            [
                'name' => 'Nature Collection Stickers',
                'slug' => 'nature-collection-stickers',
                'description' => 'Nature-themed sticker collection',
                'long_description' => 'A collection of nature-inspired die-cut stickers featuring mountains, trees, wildlife, and PNW landmarks.',
                'sku' => 'PF-STICK-NAT-001',
                'price' => 5.99,
                'cost' => 1.95,
                'base_cost' => 1.95,
                'profit_margin' => 67.45,
                'printful_product_id' => 358,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['stickers', 'accessories'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '3x3"', 'printful_cost' => 1.95, 'retail_price' => 5.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '4x4"', 'printful_cost' => 2.45, 'retail_price' => 6.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '5.5x5.5"', 'printful_cost' => 2.95, 'retail_price' => 7.99],
                ],
            ],

            // ============================================================
            // MUGS & DRINKWARE (3 products)
            // ============================================================
            [
                'name' => 'Morning Brew 11oz Mug',
                'slug' => 'morning-brew-11oz-mug',
                'description' => 'Custom printed ceramic mug',
                'long_description' => 'Start your morning right with this 11oz ceramic mug featuring a cozy coffee-themed design. Microwave and dishwasher safe.',
                'sku' => 'PF-MUG-BREW-001',
                'price' => 16.99,
                'cost' => 5.45,
                'base_cost' => 5.45,
                'profit_margin' => 67.92,
                'printful_product_id' => 19,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['mugs-drinkware', 'home-living'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '11oz', 'printful_cost' => 5.45, 'retail_price' => 16.99],
                ],
            ],
            [
                'name' => 'Portland Skyline 15oz Mug',
                'slug' => 'portland-skyline-15oz-mug',
                'description' => 'Portland skyline ceramic mug',
                'long_description' => 'A large 15oz mug featuring the iconic Portland skyline with Mt. Hood in the background. Perfect for your favorite hot beverages.',
                'sku' => 'PF-MUG-PDX-001',
                'price' => 19.99,
                'cost' => 6.95,
                'base_cost' => 6.95,
                'profit_margin' => 65.23,
                'printful_product_id' => 382,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['mugs-drinkware', 'home-living'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '15oz', 'printful_cost' => 6.95, 'retail_price' => 19.99],
                ],
            ],
            [
                'name' => 'Motivational Quote 11oz Mug',
                'slug' => 'motivational-quote-11oz-mug',
                'description' => 'Inspirational quote mug',
                'long_description' => 'Get motivated every morning with this inspirational quote mug. Clean typography on a classic white ceramic mug.',
                'sku' => 'PF-MUG-MOTV-001',
                'price' => 16.99,
                'cost' => 5.45,
                'base_cost' => 5.45,
                'profit_margin' => 67.92,
                'printful_product_id' => 19,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['mugs-drinkware', 'home-living'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '11oz', 'printful_cost' => 5.45, 'retail_price' => 16.99],
                ],
            ],

            // ============================================================
            // POSTERS & WALL ART (3 products)
            // ============================================================
            [
                'name' => 'Mountain Panorama Poster',
                'slug' => 'mountain-panorama-poster',
                'description' => 'Stunning mountain landscape poster',
                'long_description' => 'Bring the outdoors in with this stunning mountain panorama poster. Museum-quality matte finish on premium paper.',
                'sku' => 'PF-POST-MTPAN-001',
                'price' => 19.99,
                'cost' => 6.50,
                'base_cost' => 6.50,
                'profit_margin' => 67.48,
                'printful_product_id' => 1,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['posters-wall-art', 'home-living'],
                'variants' => [
                    ['color_name' => 'Matte', 'color_hex' => '#F5F5F5', 'size' => '12x18"', 'printful_cost' => 6.50, 'retail_price' => 19.99],
                    ['color_name' => 'Matte', 'color_hex' => '#F5F5F5', 'size' => '18x24"', 'printful_cost' => 9.50, 'retail_price' => 29.99],
                    ['color_name' => 'Matte', 'color_hex' => '#F5F5F5', 'size' => '24x36"', 'printful_cost' => 12.50, 'retail_price' => 39.99],
                ],
            ],
            [
                'name' => 'Abstract Geometric Canvas',
                'slug' => 'abstract-geometric-canvas',
                'description' => 'Modern abstract canvas print',
                'long_description' => 'A bold abstract geometric design on gallery-wrapped canvas. Ready to hang with no frame needed. Vivid colors on premium cotton blend canvas.',
                'sku' => 'PF-CANV-GEO-001',
                'price' => 39.99,
                'cost' => 12.50,
                'base_cost' => 12.50,
                'profit_margin' => 68.74,
                'printful_product_id' => 394,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => true,
                'status' => 'active',
                'category_slugs' => ['posters-wall-art', 'home-living'],
                'variants' => [
                    ['color_name' => 'Canvas', 'color_hex' => '#FAF0E6', 'size' => '12x12"', 'printful_cost' => 12.50, 'retail_price' => 39.99],
                    ['color_name' => 'Canvas', 'color_hex' => '#FAF0E6', 'size' => '16x16"', 'printful_cost' => 16.50, 'retail_price' => 49.99],
                ],
            ],
            [
                'name' => 'PNW Map Print',
                'slug' => 'pnw-map-print',
                'description' => 'Illustrated Pacific Northwest map',
                'long_description' => 'A beautifully illustrated map of the Pacific Northwest featuring major landmarks, cities, and natural wonders. Museum-quality poster print.',
                'sku' => 'PF-POST-MAP-001',
                'price' => 24.99,
                'cost' => 8.50,
                'base_cost' => 8.50,
                'profit_margin' => 65.99,
                'printful_product_id' => 1,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['posters-wall-art', 'home-living'],
                'variants' => [
                    ['color_name' => 'Matte', 'color_hex' => '#F5F5F5', 'size' => '12x18"', 'printful_cost' => 6.50, 'retail_price' => 19.99],
                    ['color_name' => 'Matte', 'color_hex' => '#F5F5F5', 'size' => '18x24"', 'printful_cost' => 9.50, 'retail_price' => 29.99],
                ],
            ],

            // ============================================================
            // PILLOWS (1 product)
            // ============================================================
            [
                'name' => 'Botanical Print Pillow',
                'slug' => 'botanical-print-pillow',
                'description' => 'Custom botanical throw pillow',
                'long_description' => 'Add a touch of nature to your living space with this botanical print throw pillow. Double-sided print on soft spun polyester with a hidden zipper.',
                'sku' => 'PF-PILL-BOT-001',
                'price' => 29.99,
                'cost' => 11.95,
                'base_cost' => 11.95,
                'profit_margin' => 60.15,
                'printful_product_id' => 83,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['pillows', 'home-living'],
                'variants' => [
                    ['color_name' => 'Botanical', 'color_hex' => '#228B22', 'size' => '18x18"', 'printful_cost' => 11.95, 'retail_price' => 29.99],
                    ['color_name' => 'Botanical', 'color_hex' => '#228B22', 'size' => '22x22"', 'printful_cost' => 14.95, 'retail_price' => 34.99],
                ],
            ],

            // ============================================================
            // BLANKETS (1 product)
            // ============================================================
            [
                'name' => 'Cozy Mountain Fleece Blanket',
                'slug' => 'cozy-mountain-fleece-blanket',
                'description' => 'Soft fleece blanket with mountain design',
                'long_description' => 'Wrap up in this ultra-soft fleece blanket featuring a beautiful mountain landscape. Perfect for the couch, camping, or as a decorative throw.',
                'sku' => 'PF-BLANK-MTN-001',
                'price' => 49.99,
                'cost' => 19.95,
                'base_cost' => 19.95,
                'profit_margin' => 60.09,
                'printful_product_id' => 486,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['blankets', 'home-living'],
                'variants' => [
                    ['color_name' => 'Mountain', 'color_hex' => '#4682B4', 'size' => '50x60"', 'printful_cost' => 19.95, 'retail_price' => 49.99],
                    ['color_name' => 'Mountain', 'color_hex' => '#4682B4', 'size' => '60x80"', 'printful_cost' => 24.95, 'retail_price' => 59.99],
                ],
            ],

            // ============================================================
            // KIDS' T-SHIRTS (1 product)
            // ============================================================
            [
                'name' => 'Little Explorer Kids Tee',
                'slug' => 'little-explorer-kids-tee',
                'description' => 'Adventure-themed kids tee',
                'long_description' => 'Inspire the next generation of explorers with this adorable kids tee. Features a fun adventure-themed graphic on soft, durable cotton.',
                'sku' => 'PF-KIDS-EXP-001',
                'price' => 19.99,
                'cost' => 7.50,
                'base_cost' => 7.50,
                'profit_margin' => 62.48,
                'printful_product_id' => 305,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['kids-t-shirts', 'kids-baby'],
                'variants' => $this->expandVariants(
                    ['White' => '#FFFFFF', 'Heather Gray' => '#9B9B9B'],
                    ['XS', 'S', 'M', 'L'],
                    7.50, 19.99
                ),
            ],

            // ============================================================
            // BABY ONESIES (1 product)
            // ============================================================
            [
                'name' => 'Mini Adventurer Baby Onesie',
                'slug' => 'mini-adventurer-baby-onesie',
                'description' => 'Cute adventure-themed onesie',
                'long_description' => 'The cutest little adventurer outfit! This soft cotton onesie features an adorable mountain and tent graphic perfect for your little explorer.',
                'sku' => 'PF-BABY-ADV-001',
                'price' => 18.99,
                'cost' => 7.25,
                'base_cost' => 7.25,
                'profit_margin' => 61.82,
                'printful_product_id' => 308,
                'fulfillment_type' => 'printful',
                'stock_quantity' => 999,
                'low_stock_threshold' => 0,
                'featured' => false,
                'status' => 'active',
                'category_slugs' => ['baby-onesies', 'kids-baby'],
                'variants' => [
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '3-6m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '6-12m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                    ['color_name' => 'White', 'color_hex' => '#FFFFFF', 'size' => '12-18m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                    ['color_name' => 'Heather Gray', 'color_hex' => '#9B9B9B', 'size' => '3-6m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                    ['color_name' => 'Heather Gray', 'color_hex' => '#9B9B9B', 'size' => '6-12m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                    ['color_name' => 'Heather Gray', 'color_hex' => '#9B9B9B', 'size' => '12-18m', 'printful_cost' => 7.25, 'retail_price' => 18.99],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $categorySlugs = $productData['category_slugs'];
            $variantData = $productData['variants'];
            unset($productData['category_slugs'], $productData['variants']);

            $product = Product::create($productData);
            $productsCreated++;

            // Assign categories
            foreach ($categorySlugs as $index => $categorySlug) {
                if ($category = $categories->get($categorySlug)) {
                    $product->categories()->attach($category->id, [
                        'is_primary' => $index === 0,
                        'display_order' => $index + 1,
                    ]);
                }
            }

            // Create variants
            $sortOrder = 1;
            foreach ($variantData as $variant) {
                $variantIdCounter++;
                ProductVariant::create([
                    'product_id' => $product->id,
                    'printful_variant_id' => $variantIdCounter,
                    'color_name' => $variant['color_name'],
                    'color_hex' => $variant['color_hex'],
                    'size' => $variant['size'],
                    'sku' => 'PF-' . $variantIdCounter,
                    'printful_cost' => $variant['printful_cost'],
                    'retail_price' => $variant['retail_price'],
                    'is_active' => true,
                    'stock_status' => 'in_stock',
                    'sort_order' => $sortOrder++,
                ]);
                $variantsCreated++;
            }

            // Create mockup image
            if (isset($mockupImages[$product->slug])) {
                ProductMockup::create([
                    'product_id' => $product->id,
                    'mockup_url' => $mockupImages[$product->slug],
                    'is_primary' => true,
                    'placement' => 'front',
                    'sort_order' => 1,
                ]);
            }
        }

        $this->command->info("  Created {$productsCreated} POD products with {$variantsCreated} variants and mockup images");
    }

    /**
     * Expand color/size combos into variant arrays.
     * Adds $2 to cost and $3 to retail for 2XL+ sizes.
     */
    private function expandVariants(array $colors, array $sizes, float $baseCost, float $baseRetail): array
    {
        $variants = [];
        $oversizedSizes = ['2XL', '3XL', '4XL', '5XL'];

        foreach ($colors as $colorName => $colorHex) {
            foreach ($sizes as $size) {
                $isOversized = in_array($size, $oversizedSizes);
                $variants[] = [
                    'color_name' => $colorName,
                    'color_hex' => $colorHex,
                    'size' => $size,
                    'printful_cost' => $isOversized ? $baseCost + 2.00 : $baseCost,
                    'retail_price' => $isOversized ? $baseRetail + 3.00 : $baseRetail,
                ];
            }
        }

        return $variants;
    }
}
