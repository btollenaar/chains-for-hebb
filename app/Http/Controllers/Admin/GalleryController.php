<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Services\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function __construct(
        protected GalleryService $galleryService
    ) {}

    public function index()
    {
        $albums = GalleryAlbum::ordered()
            ->withCount('photos')
            ->paginate(15);

        return view('admin.gallery.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|max:5120',
            'album_date' => 'nullable|date',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('gallery/covers', 'public');
        }

        $album = GalleryAlbum::create($validated);

        return redirect()->route('admin.gallery.show', $album)->with('success', 'Album created.');
    }

    public function show(GalleryAlbum $album)
    {
        $album->load(['photos' => fn ($q) => $q->ordered()]);
        return view('admin.gallery.show', compact('album'));
    }

    public function edit(GalleryAlbum $album)
    {
        return view('admin.gallery.edit', compact('album'));
    }

    public function update(Request $request, GalleryAlbum $album)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'cover_image' => 'nullable|image|max:5120',
            'album_date' => 'nullable|date',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('gallery/covers', 'public');
        }

        $album->update($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Album updated.');
    }

    public function destroy(GalleryAlbum $album)
    {
        foreach ($album->photos as $photo) {
            $this->galleryService->deletePhoto($photo);
        }
        $album->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Album deleted.');
    }

    public function uploadPhotos(Request $request, GalleryAlbum $album)
    {
        $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:10240',
            'photo_type' => 'nullable|in:before,during,after,event',
        ]);

        $type = $request->input('photo_type', 'during');

        foreach ($request->file('photos') as $file) {
            $this->galleryService->uploadPhoto($album, $file, ['photo_type' => $type]);
        }

        return redirect()->route('admin.gallery.show', $album)->with('success', count($request->file('photos')) . ' photos uploaded.');
    }

    public function destroyPhoto(GalleryAlbum $album, GalleryPhoto $photo)
    {
        $this->galleryService->deletePhoto($photo);
        return redirect()->route('admin.gallery.show', $album)->with('success', 'Photo deleted.');
    }
}
