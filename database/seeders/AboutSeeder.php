<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'name' => 'PrintStore Team',
            'credentials' => 'Design • Fulfillment • Customer Support',
            'short_bio' => 'We\'re a small team passionate about print-on-demand merch, helping creators and brands bring their designs to life on quality products.',
            'bio' => "PrintStore is run by a dedicated team of designers and e-commerce specialists who curate the best print-on-demand products. We work directly with Printful to ensure every item meets our quality standards before it ships to your door.\n\nOur approach is simple: great designs on great products with reliable fulfillment. We handle the production, printing, and shipping so you can focus on what matters—wearing and sharing merch you love.",
            'published' => true,
        ]);
    }
}
