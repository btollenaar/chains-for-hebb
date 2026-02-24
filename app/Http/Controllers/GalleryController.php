<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;

class GalleryController extends Controller
{
    /**
     * Display album grid.
     */
    public function index()
    {
        $albums = GalleryAlbum::published()
            ->ordered()
            ->withCount('photos')
            ->paginate(12);

        return view('gallery.index', compact('albums'));
    }

    /**
     * Display album detail with photos.
     */
    public function show(GalleryAlbum $album)
    {
        if (!$album->is_published) {
            abort(404);
        }

        $album->load(['photos' => fn ($q) => $q->ordered()]);

        return view('gallery.show', compact('album'));
    }
}
