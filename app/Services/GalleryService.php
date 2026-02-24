<?php

namespace App\Services;

use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class GalleryService
{
    /**
     * Upload a photo to an album.
     */
    public function uploadPhoto(GalleryAlbum $album, UploadedFile $file, array $data = []): GalleryPhoto
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = "gallery/{$album->id}/{$filename}";

        // Store original
        Storage::disk('public')->put($path, file_get_contents($file));

        // Generate thumbnail
        $thumbnailPath = "gallery/{$album->id}/thumbs/{$filename}";
        $this->generateThumbnail($file, $thumbnailPath);

        // Get next sort order
        $maxSort = GalleryPhoto::where('gallery_album_id', $album->id)->max('sort_order') ?? 0;

        return GalleryPhoto::create([
            'gallery_album_id' => $album->id,
            'file_path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'caption' => $data['caption'] ?? null,
            'alt_text' => $data['alt_text'] ?? $file->getClientOriginalName(),
            'photo_type' => $data['photo_type'] ?? 'during',
            'sort_order' => $maxSort + 1,
            'is_featured' => $data['is_featured'] ?? false,
        ]);
    }

    /**
     * Delete a photo and its files.
     */
    public function deletePhoto(GalleryPhoto $photo): void
    {
        Storage::disk('public')->delete($photo->file_path);
        if ($photo->thumbnail_path) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }
        $photo->delete();
    }

    /**
     * Reorder photos within an album.
     */
    public function reorderPhotos(array $photoIds): void
    {
        foreach ($photoIds as $index => $id) {
            GalleryPhoto::where('id', $id)->update(['sort_order' => $index]);
        }
    }

    /**
     * Get featured photos across all albums.
     */
    public function getFeaturedPhotos(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return GalleryPhoto::featured()
            ->whereHas('album', fn ($q) => $q->published())
            ->with('album')
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * Generate a thumbnail image.
     */
    protected function generateThumbnail(UploadedFile $file, string $path): void
    {
        try {
            // Simple copy for now — can use Intervention Image if installed
            $thumbContent = file_get_contents($file);
            Storage::disk('public')->put($path, $thumbContent);
        } catch (\Exception $e) {
            // Thumbnail generation failed — continue without thumbnail
            \Log::warning("Thumbnail generation failed: {$e->getMessage()}");
        }
    }
}
