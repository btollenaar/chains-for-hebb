<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletter;
use App\Mail\NewsletterMail;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscription;
use App\Models\SubscriberList;
use App\Services\HtmlPurifierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NewsletterCampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::with('creator', 'lists');

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $campaigns = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Newsletter::count(),
            'drafts' => Newsletter::draft()->count(),
            'scheduled' => Newsletter::scheduled()->count(),
            'sent' => Newsletter::sent()->count(),
            'avg_open_rate' => Newsletter::sent()
                ->where('sent_count', '>', 0)
                ->get()
                ->avg('open_rate') ?? 0,
        ];

        return view('admin.newsletters.campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        $lists = SubscriberList::all();
        return view('admin.newsletters.campaigns.create', compact('lists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'preview_text' => 'nullable|string|max:255',
            'content' => 'required|string',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'lists' => 'required|array|min:1',
            'lists.*' => 'exists:subscriber_lists,id',
            'action' => 'required|in:save_draft,schedule,send_now',
            'scheduled_at' => 'required_if:action,schedule|nullable|date|after:now',
        ]);

        $purifier = new HtmlPurifierService();
        $validated['content'] = $purifier->clean($validated['content']);

        $recipientCount = NewsletterSubscription::active()
            ->whereHas('lists', function ($q) use ($validated) {
                $q->whereIn('subscriber_list_id', $validated['lists']);
            })
            ->distinct()
            ->count();

        $newsletter = Newsletter::create([
            'subject' => $validated['subject'],
            'preview_text' => $validated['preview_text'] ?? null,
            'content' => $validated['content'],
            'plain_text_content' => (new Newsletter(['content' => $validated['content']]))->generatePlainText(),
            'status' => $validated['action'] === 'save_draft' ? 'draft' : ($validated['action'] === 'schedule' ? 'scheduled' : 'draft'),
            'scheduled_at' => $validated['action'] === 'schedule' ? $validated['scheduled_at'] : null,
            'recipient_count' => $recipientCount,
            'created_by' => Auth::id(),
            'from_name' => $validated['from_name'] ?? null,
            'from_email' => $validated['from_email'] ?? null,
        ]);

        $newsletter->lists()->sync($validated['lists']);

        if ($validated['action'] === 'send_now') {
            SendNewsletter::dispatch($newsletter);
            return redirect()->route('admin.newsletters.campaigns.show', $newsletter)
                ->with('success', 'Newsletter is being sent to ' . $recipientCount . ' subscribers');
        }

        $message = $validated['action'] === 'schedule'
            ? 'Newsletter scheduled successfully'
            : 'Newsletter draft saved successfully';

        return redirect()->route('admin.newsletters.campaigns.show', $newsletter)
            ->with('success', $message);
    }

    public function show(Newsletter $campaign)
    {
        $campaign->load('creator', 'lists', 'sends.subscription');

        $recentSends = $campaign->sends()
            ->with('subscription')
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.newsletters.campaigns.show', compact('campaign', 'recentSends'));
    }

    public function edit(Newsletter $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('admin.newsletters.campaigns.show', $campaign)
                ->with('error', 'Only draft campaigns can be edited');
        }

        $lists = SubscriberList::all();
        $selectedLists = $campaign->lists->pluck('id')->toArray();

        return view('admin.newsletters.campaigns.edit', compact('campaign', 'lists', 'selectedLists'));
    }

    public function update(Request $request, Newsletter $campaign)
    {
        if ($campaign->status !== 'draft') {
            return redirect()->route('admin.newsletters.campaigns.show', $campaign)
                ->with('error', 'Only draft campaigns can be edited');
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'preview_text' => 'nullable|string|max:255',
            'content' => 'required|string',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'lists' => 'required|array|min:1',
            'lists.*' => 'exists:subscriber_lists,id',
            'action' => 'required|in:save_draft,schedule,send_now',
            'scheduled_at' => 'required_if:action,schedule|nullable|date|after:now',
        ]);

        $purifier = new HtmlPurifierService();
        $validated['content'] = $purifier->clean($validated['content']);

        $recipientCount = NewsletterSubscription::active()
            ->whereHas('lists', function ($q) use ($validated) {
                $q->whereIn('subscriber_list_id', $validated['lists']);
            })
            ->distinct()
            ->count();

        $campaign->update([
            'subject' => $validated['subject'],
            'preview_text' => $validated['preview_text'] ?? null,
            'content' => $validated['content'],
            'plain_text_content' => (new Newsletter(['content' => $validated['content']]))->generatePlainText(),
            'status' => $validated['action'] === 'save_draft' ? 'draft' : ($validated['action'] === 'schedule' ? 'scheduled' : 'draft'),
            'scheduled_at' => $validated['action'] === 'schedule' ? $validated['scheduled_at'] : null,
            'recipient_count' => $recipientCount,
            'from_name' => $validated['from_name'] ?? null,
            'from_email' => $validated['from_email'] ?? null,
        ]);

        $campaign->lists()->sync($validated['lists']);

        if ($validated['action'] === 'send_now') {
            SendNewsletter::dispatch($campaign);
            return redirect()->route('admin.newsletters.campaigns.show', $campaign)
                ->with('success', 'Newsletter is being sent to ' . $recipientCount . ' subscribers');
        }

        $message = $validated['action'] === 'schedule'
            ? 'Newsletter scheduled successfully'
            : 'Newsletter updated successfully';

        return redirect()->route('admin.newsletters.campaigns.show', $campaign)
            ->with('success', $message);
    }

    public function destroy(Newsletter $campaign)
    {
        if (!in_array($campaign->status, ['draft', 'cancelled'])) {
            return redirect()->route('admin.newsletters.campaigns.index')
                ->with('error', 'Only draft or cancelled campaigns can be deleted');
        }

        $campaign->delete();

        return redirect()->route('admin.newsletters.campaigns.index')
            ->with('success', 'Campaign deleted successfully');
    }

    public function duplicate(Newsletter $campaign)
    {
        $copy = $campaign->duplicate();

        return redirect()->route('admin.newsletters.campaigns.edit', $copy)
            ->with('success', 'Campaign duplicated successfully');
    }

    public function sendTest(Request $request, Newsletter $campaign)
    {
        $validated = $request->validate([
            'test_emails' => 'required|string',
        ]);

        $emails = array_map('trim', explode(',', $validated['test_emails']));
        $emails = array_filter($emails, 'filter_var', FILTER_VALIDATE_EMAIL);

        if (empty($emails)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid email addresses provided'
            ], 422);
        }

        $campaign->load('lists');

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new NewsletterMail($campaign, null, true));
            } catch (\Exception $e) {
                Log::error('Test newsletter send failed', [
                    'campaign_id' => $campaign->id,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Test email sent to ' . count($emails) . ' recipient(s)'
        ]);
    }

    public function preview(Newsletter $campaign)
    {
        $html = view('emails.newsletter', [
            'content' => $campaign->content,
            'subject' => $campaign->subject,
            'previewText' => $campaign->preview_text,
            'unsubscribeUrl' => '#',
            'trackOpenUrl' => '#',
            'isTest' => true,
        ])->render();

        return response($html);
    }

    public function cancel(Newsletter $campaign)
    {
        if ($campaign->status !== 'scheduled') {
            return redirect()->route('admin.newsletters.campaigns.show', $campaign)
                ->with('error', 'Only scheduled campaigns can be cancelled');
        }

        $campaign->update(['status' => 'cancelled']);

        return redirect()->route('admin.newsletters.campaigns.show', $campaign)
            ->with('success', 'Campaign cancelled successfully');
    }
}
