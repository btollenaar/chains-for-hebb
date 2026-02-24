<?php

namespace Database\Seeders;

use App\Models\FundraisingBreakdown;
use Illuminate\Database\Seeder;

class FundraisingBreakdownSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['label' => '18 DISCatcher baskets', 'amount' => 5400, 'description' => 'Tournament-quality Innova DISCatcher Pro 28 baskets', 'color' => '#2D5016', 'sort_order' => 1],
            ['label' => 'Trail clearing & brush removal', 'amount' => 2400, 'description' => 'Equipment rental and contractor assistance for major clearing', 'color' => '#3D6B1E', 'sort_order' => 2],
            ['label' => '18 natural/gravel tee pads', 'amount' => 1800, 'description' => 'Compacted gravel tee pads, 5x12 feet each', 'color' => '#8B6914', 'sort_order' => 3],
            ['label' => 'Course design', 'amount' => 1500, 'description' => 'Professional course design consultation', 'color' => '#A67C1A', 'sort_order' => 4],
            ['label' => 'Gravel & drainage', 'amount' => 1500, 'description' => 'Pathway gravel and drainage improvements', 'color' => '#E85D04', 'sort_order' => 5],
            ['label' => 'Tee signs', 'amount' => 900, 'description' => '18 tee signs with hole maps, distance, and par', 'color' => '#D14F00', 'sort_order' => 6],
            ['label' => 'Benches & amenities', 'amount' => 900, 'description' => 'Benches at select tee pads and rest areas', 'color' => '#2D8B46', 'sort_order' => 7],
            ['label' => 'Signage & kiosk', 'amount' => 600, 'description' => 'Course entrance kiosk with map and rules', 'color' => '#1A7A38', 'sort_order' => 8],
        ];

        foreach ($items as $item) {
            FundraisingBreakdown::create($item);
        }
    }
}
