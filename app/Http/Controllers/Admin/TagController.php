<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tags = $query->withCount('customers')
            ->ordered()
            ->paginate(20)
            ->appends($request->all());

        $stats = [
            'total_tags' => Tag::count(),
            'total_assignments' => \DB::table('customer_tag')->count(),
        ];

        return view('admin.tags.index', compact('tags', 'stats'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => 'nullable|string|max:1000',
        ]);

        Tag::create($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'color' => ['required', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => 'nullable|string|max:1000',
        ]);

        $tag->update($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }

    /**
     * Toggle tag assignment on a customer (AJAX).
     */
    public function assignToCustomer(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tag_id' => 'required|exists:tags,id',
        ]);

        $customerId = $request->customer_id;
        $tagId = $request->tag_id;

        $exists = \DB::table('customer_tag')
            ->where('customer_id', $customerId)
            ->where('tag_id', $tagId)
            ->exists();

        if ($exists) {
            \DB::table('customer_tag')
                ->where('customer_id', $customerId)
                ->where('tag_id', $tagId)
                ->delete();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Tag removed from customer.',
            ]);
        }

        \DB::table('customer_tag')->insert([
            'customer_id' => $customerId,
            'tag_id' => $tagId,
            'assigned_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'action' => 'assigned',
            'message' => 'Tag assigned to customer.',
        ]);
    }

    /**
     * Bulk assign a tag to multiple customers.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'tag_id' => 'required|exists:tags,id',
            'customer_ids' => 'required|array|min:1',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $tagId = $request->tag_id;
        $assignedCount = 0;

        foreach ($request->customer_ids as $customerId) {
            $exists = \DB::table('customer_tag')
                ->where('customer_id', $customerId)
                ->where('tag_id', $tagId)
                ->exists();

            if (!$exists) {
                \DB::table('customer_tag')->insert([
                    'customer_id' => $customerId,
                    'tag_id' => $tagId,
                    'assigned_by' => auth()->id(),
                    'created_at' => now(),
                ]);
                $assignedCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Tag assigned to {$assignedCount} customer(s).");
    }
}
