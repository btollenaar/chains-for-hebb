<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Jobs\FulfillOrder;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\CouponService;
use App\Services\LoyaltyService;
use App\Services\OrderFactory;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Services\TaxJarService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;
    protected CouponService $couponService;
    protected OrderFactory $orderFactory;
    protected PaymentService $paymentService;
    protected ShippingService $shippingService;

    public function __construct(
        CheckoutService $checkoutService,
        CouponService $couponService,
        OrderFactory $orderFactory,
        PaymentService $paymentService,
        ShippingService $shippingService
    ) {
        $this->checkoutService = $checkoutService;
        $this->couponService = $couponService;
        $this->orderFactory = $orderFactory;
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
    }

    /**
     * Display the checkout form
     */
    public function index()
    {
        // Get cart items
        $cartItems = $this->checkoutService->getCartItems(
            $this->getCartIdentifier(),
            Auth::check()
        );

        // Check if cart is empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        // Calculate totals
        $totals = $this->orderFactory->calculateOrderTotals($cartItems);

        // Get customer data for pre-population
        $customer = Auth::check() ? Auth::user() : null;

        // PayPal integration not yet implemented
        $paypalAvailable = false;

        // Check for promo code in URL (e.g., /checkout?promo=WELCOME10)
        $promoCode = request('promo');
        $promoResult = null;
        if ($promoCode) {
            $promoResult = $this->couponService->validateCoupon(
                $promoCode,
                $totals['subtotal'],
                Auth::id()
            );
        }

        // Calculate shipping
        $totalWeight = $this->shippingService->calculateTotalWeight($cartItems);
        $shippingMethods = $this->shippingService->getShippingRates($totals['subtotal'], $totalWeight);

        // Loyalty points
        $loyaltyBalance = Auth::check() ? Auth::user()->loyalty_points_balance : 0;
        $loyaltyService = app(LoyaltyService::class);
        $maxRedeemable = Auth::check() ? $loyaltyService->getMaxRedeemablePoints(Auth::user(), $totals['subtotal']) : 0;
        $pointsPerDollar = (int) Setting::get('loyalty.points_per_dollar_discount', 100);

        return view('checkout.index', [
            'cartItems' => $cartItems,
            'subtotal' => $totals['subtotal'],
            'tax' => $totals['tax'],
            'total' => $totals['total'],
            'customer' => $customer,
            'paypalAvailable' => $paypalAvailable,
            'promoCode' => $promoCode,
            'promoResult' => $promoResult,
            'shippingMethods' => $shippingMethods,
            'totalWeight' => $totalWeight,
            'loyaltyBalance' => $loyaltyBalance,
            'maxRedeemable' => $maxRedeemable,
            'pointsPerDollar' => $pointsPerDollar,
        ]);
    }

    /**
     * Process the checkout and create order
     */
    public function process(CheckoutRequest $request)
    {
        // Get cart items
        $cartItems = $this->checkoutService->getCartItems(
            $this->getCartIdentifier(),
            Auth::check()
        );

        // Validate cart not empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Validate stock availability
        $stockError = $this->checkoutService->validateStockAvailability($cartItems);
        if ($stockError) {
            return redirect()->route('cart.index')->with($stockError);
        }

        // Get validated data from form request
        $validated = $request->validated();

        // Validate coupon if provided
        $coupon = null;
        if (!empty($validated['coupon_code'])) {
            $totals = $this->orderFactory->calculateOrderTotals($cartItems);
            $couponResult = $this->couponService->validateCoupon(
                $validated['coupon_code'],
                $totals['subtotal'],
                Auth::id()
            );

            if (!$couponResult['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Coupon error: ' . $couponResult['error']);
            }

            $coupon = $couponResult['coupon'];
        }

        try {
            DB::beginTransaction();

            // Find or create customer
            $customer = $this->checkoutService->findOrCreateCustomer($validated);

            // Build address arrays
            $addresses = $this->orderFactory->buildAddressArrays(
                $validated,
                $request->boolean('same_as_shipping')
            );

            // Calculate shipping
            $totalWeight = $this->shippingService->calculateTotalWeight($cartItems);
            $subtotalTotals = $this->orderFactory->calculateOrderTotals($cartItems);
            $shippingCost = $this->shippingService->getShippingCost(
                $validated['shipping_method'],
                $subtotalTotals['subtotal'],
                $totalWeight
            );

            // Recalculate totals with shipping and address for accurate tax
            $totals = $this->orderFactory->calculateOrderTotals($cartItems, $shippingCost, $addresses['shipping']);

            // Create order from cart
            $order = $this->orderFactory->createOrderFromCart(
                $customer,
                $cartItems,
                $addresses['shipping'],
                $addresses['billing'],
                $validated['payment_method'],
                $validated['notes'] ?? null,
                $coupon,
                $shippingCost,
                $validated['shipping_method'],
                $totalWeight
            );

            // Handle loyalty points redemption
            if (Auth::check() && $request->filled('redeem_points') && (int) $request->redeem_points > 0) {
                $loyaltyService = app(LoyaltyService::class);
                $loyaltyService->redeemPoints(Auth::user(), (int) $request->redeem_points, $order);
                $order->refresh();
                $order->update(['total_amount' => $order->total_amount - $order->loyalty_discount]);
            }

            // Handle newsletter opt-in
            $this->checkoutService->processNewsletterOptIn(
                $request->boolean('newsletter_opt_in'),
                $validated['email'],
                $validated['name'],
                $customer->id
            );

            // Clear cart
            $this->checkoutService->clearCustomerCart(
                $this->getCartIdentifier(),
                Auth::check()
            );

            DB::commit();

            // Send account claim email for guest customers
            $this->checkoutService->sendAccountClaimEmail($customer, $order);

            // Notify admins of new order (fail silently)
            try {
                \App\Services\AdminNotificationService::notifyNewOrder($order);
            } catch (\Exception $e) {
                Log::error('Admin notification failed for new order', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }

            // Route to appropriate payment processor
            return $this->processPayment($order, $customer);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Checkout processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'There was an error processing your order. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Process payment based on selected method
     */
    protected function processPayment(Order $order, $customer)
    {
        try {
            switch ($order->payment_method) {
                case 'stripe':
                    $redirectUrl = $this->paymentService->processStripePayment($order, $customer);
                    return redirect($redirectUrl);

                case 'paypal':
                    $redirectUrl = $this->paymentService->processPayPalPayment($order, $customer);
                    return redirect($redirectUrl);

                case 'cash':
                case 'check':
                    // Send order confirmation email for manual payment methods
                    $this->checkoutService->sendOrderConfirmationEmail($order);

                    // Create TaxJar transaction for reporting
                    try {
                        app(TaxJarService::class)->createTransaction($order);
                    } catch (\Exception $e) {
                        Log::error('TaxJar transaction failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    }

                    // Dispatch fulfillment for manual payment orders
                    if ($order->fulfillment_status === 'pending') {
                        FulfillOrder::dispatch($order);
                    }

                    return redirect(URL::signedRoute('checkout.success', ['order' => $order->id]))
                        ->with('info', 'Your order has been placed. Please complete payment via ' . $order->payment_method . '.');

                default:
                    throw new \Exception('Invalid payment method selected.');
            }
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id ?? null,
                'payment_method' => $order->payment_method ?? null,
            ]);

            return redirect()->route('checkout.cancel')
                ->with('error', 'Payment processing failed. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Display order confirmation
     */
    public function success(Request $request, Order $order)
    {
        // Verify Stripe payment if session_id is present
        if ($request->has('session_id') && $order->payment_method === 'stripe') {
            $verified = $this->paymentService->verifyStripePayment(
                $request->session_id,
                $order
            );

            // Send order confirmation email if payment verified
            if ($verified) {
                $this->checkoutService->sendOrderConfirmationEmail($order);

                // Create TaxJar transaction for reporting
                try {
                    app(TaxJarService::class)->createTransaction($order);
                } catch (\Exception $e) {
                    Log::error('TaxJar transaction failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                }

                // Dispatch fulfillment job for paid orders
                if ($order->payment_status === 'paid' && $order->fulfillment_status === 'pending') {
                    FulfillOrder::dispatch($order);
                }

                // Earn loyalty points for paid orders
                if ($order->payment_status === 'paid' && Auth::check()) {
                    try {
                        $loyaltyService = app(LoyaltyService::class);
                        $points = $loyaltyService->calculatePointsForOrder($order);
                        if ($points > 0) {
                            $loyaltyService->earnPoints(
                                Auth::user(),
                                $points,
                                'order',
                                $order->id,
                                "Earned {$points} points from Order #{$order->order_number}"
                            );
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to earn loyalty points', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        // Load order relationships
        $order->load('items', 'customer');

        return view('checkout.success', compact('order'));
    }

    /**
     * Display checkout cancellation
     */
    public function cancel()
    {
        return view('checkout.cancel');
    }

    /**
     * Get cart identifier (user ID or session ID)
     */
    protected function getCartIdentifier()
    {
        return Auth::check() ? Auth::id() : session()->getId();
    }
}
