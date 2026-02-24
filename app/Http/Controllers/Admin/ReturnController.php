<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReturnStatusMail;
use App\Models\ReturnRequest;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $stats = ReturnRequest::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'requested' THEN 1 ELSE 0 END) as requested,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'completed' THEN refund_amount ELSE 0 END) as total_refunded
        ")->first();

        $returns = ReturnRequest::with(['order', 'customer'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('return_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                      ->orWhereHas('order', fn($q) => $q->where('order_number', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        return view('admin.returns.index', compact('returns', 'stats'));
    }

    public function show(ReturnRequest $return)
    {
        $return->load(['order.items.item', 'customer', 'processedBy']);

        return view('admin.returns.show', compact('return'));
    }

    /**
     * Approve a return request
     */
    public function approve(Request $request, ReturnRequest $return)
    {
        if ($return->status !== 'requested') {
            return redirect()->back()->with('error', 'This return request has already been processed.');
        }

        $validated = $request->validate([
            'refund_amount' => ['required', 'numeric', 'min:0', 'max:' . $return->order->total_amount],
            'refund_method' => ['required', 'in:original,store_credit,manual'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $return->approve(
            $validated['refund_amount'],
            $validated['refund_method'],
            $validated['admin_notes'] ?? null,
            Auth::id()
        );

        // Process refund if using original payment method and order was paid via Stripe
        if ($validated['refund_method'] === 'original' && $return->order->payment_method === 'stripe' && $return->order->stripe_payment_intent_id) {
            try {
                $paymentService = app(PaymentService::class);
                $paymentService->processStripeRefund($return->order, $validated['refund_amount']);
                $return->markCompleted();
            } catch (\Exception $e) {
                Log::error('Stripe refund failed', [
                    'return_id' => $return->id,
                    'order_id' => $return->order_id,
                    'error' => $e->getMessage(),
                ]);
                // Return approved but refund needs manual processing
            }
        }

        // Send status email
        try {
            Mail::to($return->customer->email)->send(new ReturnStatusMail($return));
        } catch (\Exception $e) {
            Log::error('Return status email failed', ['return_id' => $return->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Return request approved.');
    }

    /**
     * Reject a return request
     */
    public function reject(Request $request, ReturnRequest $return)
    {
        if ($return->status !== 'requested') {
            return redirect()->back()->with('error', 'This return request has already been processed.');
        }

        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:2000'],
        ]);

        $return->reject($validated['admin_notes'], Auth::id());

        // Send status email
        try {
            Mail::to($return->customer->email)->send(new ReturnStatusMail($return));
        } catch (\Exception $e) {
            Log::error('Return status email failed', ['return_id' => $return->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Return request rejected.');
    }

    /**
     * Mark an approved return as completed (refund processed)
     */
    public function complete(ReturnRequest $return)
    {
        if ($return->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved returns can be marked as completed.');
        }

        $return->markCompleted();

        // Send completion email
        try {
            Mail::to($return->customer->email)->send(new ReturnStatusMail($return));
        } catch (\Exception $e) {
            Log::error('Return completion email failed', ['return_id' => $return->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.returns.show', $return)
            ->with('success', 'Return marked as completed. Refund has been processed.');
    }
}
