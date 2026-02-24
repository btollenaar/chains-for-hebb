<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::topLevel()
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        $parentPages = CmsPage::topLevel()->orderBy('sort_order')->get();
        return view('admin.pages.create', compact('parentPages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:5120',
            'template' => 'nullable|in:default,faq,course-plan,how-to-help',
            'is_published' => 'nullable|boolean',
            'show_in_nav' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:cms_pages,id',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['show_in_nav'] = $request->boolean('show_in_nav');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        CmsPage::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(CmsPage $page)
    {
        $parentPages = CmsPage::topLevel()
            ->where('id', '!=', $page->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.pages.edit', compact('page', 'parentPages'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|max:5120',
            'template' => 'nullable|in:default,faq,course-plan,how-to-help',
            'is_published' => 'nullable|boolean',
            'show_in_nav' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:cms_pages,id',
            'sort_order' => 'nullable|integer',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['show_in_nav'] = $request->boolean('show_in_nav');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('pages', 'public');
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Page deleted.');
    }
}
