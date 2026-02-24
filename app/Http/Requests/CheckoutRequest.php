<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * CheckoutRequest
 *
 * Handles validation for checkout form submission.
 * Includes customer info, shipping/billing addresses, and payment method.
 */
class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Checkout is available to all users (authenticated and guests)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Build list of enabled payment methods from config
        $enabledMethods = collect(config('business.payments.enabled_methods'))
            ->filter(fn($enabled) => $enabled === true)
            ->keys()
            ->implode(',');

        return [
            // Customer Info
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\d\s\-\(\)\+\.]+$/'],

            // Shipping Address
            'shipping_street' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'shipping_zip' => ['required', 'string', 'max:10', 'regex:/^\d{5}(-\d{4})?$/'],
            'shipping_country' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],

            // Billing Address (conditional on same_as_shipping)
            'same_as_shipping' => 'boolean',
            'billing_street' => 'required_if:same_as_shipping,false|nullable|string|max:255',
            'billing_city' => 'required_if:same_as_shipping,false|nullable|string|max:100',
            'billing_state' => ['required_if:same_as_shipping,false', 'nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'billing_zip' => ['required_if:same_as_shipping,false', 'nullable', 'string', 'max:10', 'regex:/^\d{5}(-\d{4})?$/'],
            'billing_country' => ['required_if:same_as_shipping,false', 'nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],

            // Payment Method (dynamically validated against enabled methods)
            'payment_method' => "required|string|in:{$enabledMethods}",

            // Shipping
            'shipping_method' => 'required|string|in:free,standard,express',

            // Coupon
            'coupon_code' => 'nullable|string|max:50',

            // Loyalty Points
            'redeem_points' => 'nullable|integer|min:0',

            // Optional fields
            'notes' => 'nullable|string|max:1000',
            'newsletter_opt_in' => 'boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.regex' => 'Please enter a valid phone number.',
            'shipping_street.required' => 'Please enter your shipping address.',
            'shipping_city.required' => 'Please enter your shipping city.',
            'shipping_state.required' => 'Please select your shipping state.',
            'shipping_state.regex' => 'Please enter a valid 2-letter state code (e.g., CA, NY).',
            'shipping_zip.required' => 'Please enter your shipping ZIP code.',
            'shipping_zip.regex' => 'Please enter a valid ZIP code (e.g., 12345 or 12345-6789).',
            'shipping_country.regex' => 'Please enter a valid 2-letter country code (e.g., US).',
            'billing_street.required_if' => 'Please enter your billing address.',
            'billing_city.required_if' => 'Please enter your billing city.',
            'billing_state.required_if' => 'Please select your billing state.',
            'billing_state.regex' => 'Please enter a valid 2-letter state code (e.g., CA, NY).',
            'billing_zip.required_if' => 'Please enter your billing ZIP code.',
            'billing_zip.regex' => 'Please enter a valid ZIP code (e.g., 12345 or 12345-6789).',
            'billing_country.regex' => 'Please enter a valid 2-letter country code (e.g., US).',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is not available.',
            'shipping_method.required' => 'Please select a shipping method.',
            'shipping_method.in' => 'The selected shipping method is not available.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'shipping_street' => 'shipping address',
            'shipping_city' => 'shipping city',
            'shipping_state' => 'shipping state',
            'shipping_zip' => 'shipping ZIP code',
            'billing_street' => 'billing address',
            'billing_city' => 'billing city',
            'billing_state' => 'billing state',
            'billing_zip' => 'billing ZIP code',
        ];
    }
}
