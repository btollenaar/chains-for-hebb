<?php

namespace Database\Seeders;

use App\Models\GalleryAlbum;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        GalleryAlbum::create([
            'title' => 'Hebb Park — Before',
            'slug' => 'hebb-park-before',
            'description' => 'The park before course construction begins — trails, river views, and natural beauty.',
            'album_date' => now(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        GalleryAlbum::create([
            'title' => 'Community Events',
            'slug' => 'community-events',
            'description' => 'Photos from our fundraisers, work parties, and community meetups.',
            'album_date' => now(),
            'is_published' => true,
            'sort_order' => 2,
        ]);
    }
}
