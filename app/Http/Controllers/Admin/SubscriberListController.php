<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriberList;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SubscriberListController extends Controller
{
    /**
     * Display a listing of subscriber lists.
     */
    public function index(Request $request)
    {
        $query = SubscriberList::withCount('subscribers');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Type filter (system vs custom)
        if ($request->filled('type')) {
            if ($request->type === 'system') {
                $query->where('is_system', true);
            } elseif ($request->type === 'custom') {
                $query->where('is_system', false);
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $lists = $query->paginate(15)->withQueryString();

        return view('admin.subscriber-lists.index', compact('lists'));
    }

    /**
     * Show the form for creating a new subscriber list.
     */
    public function create()
    {
        return view('admin.subscriber-lists.create');
    }

    /**
     * Store a newly created subscriber list.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscriber_lists', 'name'),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('subscriber_lists', 'slug'),
            ],
            'description' => 'nullable|string|max:1000',
        ], [
            'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
        ]);

        // Custom lists are never system lists
        $validated['is_system'] = false;

        $list = SubscriberList::create($validated);

        Log::info('Subscriber list created', [
            'list_id' => $list->id,
            'name' => $list->name,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.subscriber-lists.index')
            ->with('success', 'Subscriber list created successfully.');
    }

    /**
     * Display the specified subscriber list.
     */
    public function show(SubscriberList $subscriberList)
    {
        $subscriberList->loadCount('subscribers');

        // Get subscribers for this list
        $query = $subscriberList->subscribers()
            ->where('is_active', true);

        // Search filter
        if (request()->filled('search')) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . request('search') . '%')
                  ->orWhere('name', 'like', '%' . request('search') . '%');
            });
        }

        $subscribers = $query->paginate(20)->withQueryString();

        return view('admin.subscriber-lists.show', compact('subscriberList', 'subscribers'));
    }

    /**
     * Show the form for editing the specified subscriber list.
     */
    public function edit(SubscriberList $subscriberList)
    {
        // Prevent editing system lists
        if ($subscriberList->is_system) {
            return redirect()->route('admin.subscriber-lists.index')
                ->with('error', 'System lists cannot be edited.');
        }

        return view('admin.subscriber-lists.edit', compact('subscriberList'));
    }

    /**
     * Update the specified subscriber list.
     */
    public function update(Request $request, SubscriberList $subscriberList)
    {
        // Prevent updating system lists
        if ($subscriberList->is_system) {
            return redirect()->route('admin.subscriber-lists.index')
                ->with('error', 'System lists cannot be modified.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subscriber_lists', 'name')->ignore($subscriberList->id),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('subscriber_lists', 'slug')->ignore($subscriberList->id),
            ],
            'description' => 'nullable|string|max:1000',
        ], [
            'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
        ]);

        $subscriberList->update($validated);

        Log::info('Subscriber list updated', [
            'list_id' => $subscriberList->id,
            'name' => $subscriberList->name,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.subscriber-lists.index')
            ->with('success', 'Subscriber list updated successfully.');
    }

    /**
     * Remove the specified subscriber list.
     */
    public function destroy(SubscriberList $subscriberList)
    {
        // Prevent deleting system lists
        if ($subscriberList->is_system) {
            return redirect()->route('admin.subscriber-lists.index')
                ->with('error', 'System lists cannot be deleted.');
        }

        $name = $subscriberList->name;

        // Detach all subscribers (pivot records) before deleting
        $subscriberList->subscribers()->detach();

        $subscriberList->delete();

        Log::info('Subscriber list deleted', [
            'list_id' => $subscriberList->id,
            'name' => $name,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.subscriber-lists.index')
            ->with('success', 'Subscriber list deleted successfully.');
    }

    /**
     * Bulk add subscribers to a list.
     */
    public function bulkAddSubscribers(Request $request, SubscriberList $subscriberList)
    {
        $validated = $request->validate([
            'subscriber_ids' => 'required|array',
            'subscriber_ids.*' => 'exists:newsletter_subscriptions,id',
        ]);

        $subscriberList->subscribers()->syncWithoutDetaching($validated['subscriber_ids']);

        Log::info('Bulk subscribers added to list', [
            'list_id' => $subscriberList->id,
            'count' => count($validated['subscriber_ids']),
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', count($validated['subscriber_ids']) . ' subscriber(s) added to list.');
    }

    /**
     * Bulk remove subscribers from a list.
     */
    public function bulkRemoveSubscribers(Request $request, SubscriberList $subscriberList)
    {
        $validated = $request->validate([
            'subscriber_ids' => 'required|array',
            'subscriber_ids.*' => 'exists:newsletter_subscriptions,id',
        ]);

        $subscriberList->subscribers()->detach($validated['subscriber_ids']);

        Log::info('Bulk subscribers removed from list', [
            'list_id' => $subscriberList->id,
            'count' => count($validated['subscriber_ids']),
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', count($validated['subscriber_ids']) . ' subscriber(s) removed from list.');
    }

    /**
     * Remove a single subscriber from a list.
     */
    public function removeSubscriber(SubscriberList $subscriberList, NewsletterSubscription $subscriber)
    {
        $subscriberList->subscribers()->detach($subscriber->id);

        Log::info('Subscriber removed from list', [
            'list_id' => $subscriberList->id,
            'subscription_id' => $subscriber->id,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Subscriber removed from list.');
    }
}
