<?php

namespace Database\Seeders;

use App\Models\FundraisingMilestone;
use Illuminate\Database\Seeder;

class FundraisingMilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            ['title' => 'Survey Complete', 'description' => 'Professional course survey and site assessment', 'target_amount' => 1500, 'icon' => 'fas fa-map-marked-alt', 'sort_order' => 1],
            ['title' => 'Permits Approved', 'description' => 'County permits and environmental review', 'target_amount' => 2000, 'icon' => 'fas fa-file-signature', 'sort_order' => 2],
            ['title' => 'Course Design Finalized', 'description' => 'Professional 18-hole layout complete', 'target_amount' => 3500, 'icon' => 'fas fa-drafting-compass', 'sort_order' => 3],
            ['title' => 'First 9 Holes Installed', 'description' => 'Front 9 baskets, tee pads, and signs', 'target_amount' => 8000, 'icon' => 'fas fa-bullseye', 'sort_order' => 4],
            ['title' => 'Back 9 Installed', 'description' => 'Full 18-hole course complete', 'target_amount' => 13000, 'icon' => 'fas fa-flag-checkered', 'sort_order' => 5],
            ['title' => 'Grand Opening', 'description' => 'Course amenities, kiosk, and opening event', 'target_amount' => 15000, 'icon' => 'fas fa-trophy', 'sort_order' => 6],
        ];

        foreach ($milestones as $milestone) {
            FundraisingMilestone::create($milestone);
        }
    }
}
