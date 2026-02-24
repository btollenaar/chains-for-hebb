<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Services\HtmlPurifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    protected HtmlPurifierService $purifier;

    public function __construct(HtmlPurifierService $purifier)
    {
        $this->purifier = $purifier;
    }
    public function index()
    {
        $query = BlogPost::with(['category', 'author']);

        // Apply category filter
        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        // Apply status filter
        if (request('status') !== null && request('status') !== '') {
            if (request('status') === 'published') {
                $query->where('published', true);
            } elseif (request('status') === 'draft') {
                $query->where('published', false);
            }
        }

        // Apply date range filter (published_at)
        if (request('date_from')) {
            $query->whereDate('published_at', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('published_at', '<=', request('date_to'));
        }

        // Apply search filter (title or content)
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $posts = $query->latest('created_at')->paginate(20);

        // Get categories for filter dropdown
        $categories = BlogCategory::orderBy('name')->pluck('name', 'id');

        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);

        // Sanitize HTML content to prevent XSS
        if (isset($validated['content'])) {
            $validated['content'] = $this->purifier->clean($validated['content']);
        }
        if (isset($validated['excerpt'])) {
            $validated['excerpt'] = $this->purifier->clean($validated['excerpt']);
        }

        $validated['author_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog', 'public');
            // Remove UploadedFile object from validated array before assigning processed path
            unset($validated['featured_image']);
            $validated['featured_image'] = $path;
        }

        if ($request->input('published')) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blog_posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);

        // Sanitize HTML content to prevent XSS
        if (isset($validated['content'])) {
            $validated['content'] = $this->purifier->clean($validated['content']);
        }
        if (isset($validated['excerpt'])) {
            $validated['excerpt'] = $this->purifier->clean($validated['excerpt']);
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('blog', 'public');
            // Remove UploadedFile object from validated array before assigning processed path
            unset($validated['featured_image']);
            $validated['featured_image'] = $path;
        }

        if ($request->input('published') && !$post->published) {
            $validated['published_at'] = now();
        } elseif (!$request->input('published')) {
            $validated['published_at'] = null;
        }

        $post->update($validated);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Apply bulk actions to multiple blog posts
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,publish,unpublish',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:blog_posts,id',
        ]);

        $count = count($validated['ids']);
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                // Get blog posts to delete their featured images
                $posts = BlogPost::whereIn('id', $validated['ids'])->get();
                foreach ($posts as $post) {
                    if ($post->featured_image) {
                        Storage::disk('public')->delete($post->featured_image);
                    }
                }
                // Soft delete blog posts
                BlogPost::whereIn('id', $validated['ids'])->delete();
                $message = "{$count} blog post(s) deleted successfully.";
                break;

            case 'publish':
                // Update published status and set published_at timestamp
                BlogPost::whereIn('id', $validated['ids'])->update([
                    'published' => true,
                    'published_at' => now(),
                ]);
                $message = "{$count} blog post(s) published successfully.";
                break;

            case 'unpublish':
                // Update published status and clear published_at timestamp
                BlogPost::whereIn('id', $validated['ids'])->update([
                    'published' => false,
                    'published_at' => null,
                ]);
                $message = "{$count} blog post(s) unpublished successfully.";
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return back()->with('success', $message);
    }
}
