<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaxJarService
{
    private ?string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.taxjar.api_key', '');
        $this->baseUrl = config('services.taxjar.sandbox', false)
            ? 'https://api.sandbox.taxjar.com/v2'
            : 'https://api.taxjar.com/v2';
    }

    /**
     * Check if TaxJar is configured and available
     */
    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && $this->apiKey !== '';
    }

    /**
     * Calculate tax for an order based on shipping address
     *
     * @return array{amount_to_collect: float, rate: float, has_nexus: bool, freight_taxable: bool}
     */
    public function calculateTax(array $orderData): array
    {
        if (!$this->isEnabled()) {
            return $this->fallbackTax($orderData);
        }

        $cacheKey = 'taxjar_' . md5(json_encode($orderData));
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $payload = [
                'from_country' => 'US',
                'from_zip' => config('business.contact.address.zip', '97015'),
                'from_state' => config('business.contact.address.state', 'OR'),
                'to_country' => $orderData['to_country'] ?? 'US',
                'to_zip' => $orderData['to_zip'],
                'to_state' => $orderData['to_state'],
                'to_city' => $orderData['to_city'] ?? '',
                'amount' => $orderData['amount'],
                'shipping' => $orderData['shipping'] ?? 0,
            ];

            if (!empty($orderData['line_items'])) {
                $payload['line_items'] = $orderData['line_items'];
            }

            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/taxes", $payload);

            if ($response->failed()) {
                Log::warning('TaxJar API request failed, using fallback tax rate', [
                    'status' => $response->status(),
                    'error' => $response->json('error') ?? $response->body(),
                ]);
                return $this->fallbackTax($orderData);
            }

            $tax = $response->json('tax', []);

            $result = [
                'amount_to_collect' => (float) ($tax['amount_to_collect'] ?? 0),
                'rate' => (float) ($tax['rate'] ?? 0),
                'has_nexus' => (bool) ($tax['has_nexus'] ?? false),
                'freight_taxable' => (bool) ($tax['freight_taxable'] ?? false),
            ];

            Cache::put($cacheKey, $result, 3600);

            return $result;
        } catch (\Exception $e) {
            Log::error('TaxJar tax calculation failed', [
                'error' => $e->getMessage(),
            ]);
            return $this->fallbackTax($orderData);
        }
    }

    /**
     * Create a transaction in TaxJar for reporting/filing
     */
    public function createTransaction(Order $order): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $lineItems = [];
            foreach ($order->items as $item) {
                $lineItems[] = [
                    'id' => (string) $item->id,
                    'quantity' => $item->quantity,
                    'product_identifier' => $item->item?->sku ?? "ITEM-{$item->id}",
                    'description' => $item->name,
                    'unit_price' => (float) $item->unit_price,
                    'sales_tax' => 0,
                ];
            }

            $response = Http::withToken($this->apiKey)
                ->post("{$this->baseUrl}/transactions/orders", [
                    'transaction_id' => "ORD-{$order->id}",
                    'transaction_date' => $order->created_at->toDateString(),
                    'from_country' => 'US',
                    'from_zip' => config('business.contact.address.zip', '97015'),
                    'from_state' => config('business.contact.address.state', 'OR'),
                    'to_country' => $order->shipping_address['country'] ?? 'US',
                    'to_zip' => $order->shipping_address['zip'] ?? '',
                    'to_state' => $order->shipping_address['state'] ?? '',
                    'to_city' => $order->shipping_address['city'] ?? '',
                    'amount' => (float) $order->total_amount,
                    'shipping' => (float) ($order->shipping_cost ?? 0),
                    'sales_tax' => (float) ($order->tax_amount ?? 0),
                    'line_items' => $lineItems,
                ]);

            if ($response->failed()) {
                Log::warning('TaxJar transaction creation failed', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'error' => $response->json('error') ?? $response->body(),
                ]);
                return null;
            }

            return $response->json('order', []);
        } catch (\Exception $e) {
            Log::error('TaxJar transaction creation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Fallback to configured flat tax rate when TaxJar is unavailable
     */
    private function fallbackTax(array $orderData): array
    {
        $taxRate = config('business.payments.tax_rate', 0.07);
        $amount = ($orderData['amount'] ?? 0) * $taxRate;

        return [
            'amount_to_collect' => round($amount, 2),
            'rate' => $taxRate,
            'has_nexus' => true,
            'freight_taxable' => false,
        ];
    }
}
