<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $stats = Coupon::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_active = 1 AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP) THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN expires_at IS NOT NULL AND expires_at <= CURRENT_TIMESTAMP THEN 1 ELSE 0 END) as expired
        ")->first();

        $totalSavings = CouponUsage::sum('discount_amount');

        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            switch ($request->input('status')) {
                case 'active':
                    $query->active()->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    });
                    break;
                case 'expired':
                    $query->where('expires_at', '<=', now());
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        return view('admin.coupons.index', compact('coupons', 'stats', 'totalSavings'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $usageHistory = $coupon->usage()
            ->with(['customer', 'order'])
            ->orderBy('used_at', 'desc')
            ->paginate(20);

        $totalSavings = $coupon->usage()->sum('discount_amount');

        return view('admin.coupons.show', compact('coupon', 'usageHistory', 'totalSavings'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_customer' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function toggleActive(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Coupon {$status} successfully.");
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="coupons-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Code', 'Description', 'Type', 'Value', 'Min Order', 'Max Discount', 'Max Uses', 'Used', 'Starts', 'Expires', 'Active']);

            Coupon::chunk(500, function ($coupons) use ($file) {
                foreach ($coupons as $coupon) {
                    fputcsv($file, [
                        $coupon->code,
                        $coupon->description,
                        $coupon->type,
                        $coupon->value,
                        $coupon->min_order_amount,
                        $coupon->max_discount_amount,
                        $coupon->max_uses,
                        $coupon->used_count,
                        $coupon->starts_at?->format('Y-m-d H:i'),
                        $coupon->expires_at?->format('Y-m-d H:i'),
                        $coupon->is_active ? 'Yes' : 'No',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
