<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(Customer::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'regex:/^[\d\s\-\(\)]+$/', 'max:20'],

            // Billing Address
            'billing_street' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_state' => ['nullable', 'string', 'max:50'],
            'billing_zip' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/', 'max:20'],
            'billing_country' => ['nullable', 'string', 'max:100'],

            // Shipping Address
            'shipping_street' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_state' => ['nullable', 'string', 'max:50'],
            'shipping_zip' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'max:100'],
        ];
    }
}
