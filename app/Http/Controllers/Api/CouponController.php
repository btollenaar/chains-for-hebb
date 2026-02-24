<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    protected CouponService $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $result = $this->couponService->validateCoupon(
            $request->input('code'),
            (float) $request->input('subtotal'),
            Auth::id()
        );

        if ($result['valid']) {
            return response()->json([
                'valid' => true,
                'discount' => $result['discount'],
                'formatted' => $result['formatted'],
                'message' => 'Coupon applied! You save ' . $result['formatted'],
            ]);
        }

        return response()->json([
            'valid' => false,
            'error' => $result['error'],
        ]);
    }
}
