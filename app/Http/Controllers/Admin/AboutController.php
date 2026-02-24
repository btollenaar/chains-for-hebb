<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Services\HtmlPurifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    protected HtmlPurifierService $purifier;

    public function __construct(HtmlPurifierService $purifier)
    {
        $this->purifier = $purifier;
    }
    /**
     * Show the form for editing the about page.
     */
    public function edit()
    {
        $about = About::first() ?? new About();
        return view('admin.about.edit', compact('about'));
    }

    /**
     * Update the about page.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'credentials' => 'nullable|string|max:255',
            'short_bio' => 'nullable|string|max:500',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Sanitize HTML content to prevent XSS (TinyMCE outputs HTML tags)
        if (isset($validated['bio'])) {
            $validated['bio'] = $this->purifier->clean($validated['bio']);
        }
        if (isset($validated['short_bio'])) {
            $validated['short_bio'] = $this->purifier->clean($validated['short_bio']);
        }
        if (isset($validated['credentials'])) {
            $validated['credentials'] = $this->purifier->clean($validated['credentials']);
        }

        // Handle checkbox - unchecked checkboxes don't send a value
        $validated['published'] = $request->has('published');

        $about = About::first() ?? new About();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($about->image) {
                Storage::disk('public')->delete($about->image);
            }

            $path = $request->file('image')->store('about', 'public');

            // Remove UploadedFile object from validated array before assigning processed path
            unset($validated['image']);
            $validated['image'] = $path;
        }

        $about->fill($validated)->save();

        return redirect()->back()->with('success', 'About page updated successfully!');
    }
}
