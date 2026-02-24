<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PrintfulCatalogCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrintfulService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.printful.com';

    public function __construct()
    {
        $this->apiKey = (string) config('services.printful.api_key', '');
    }

    // ─── Catalog Browsing ────────────────────────────────────────────────

    /**
     * Get all products from Printful's catalog (450+ items).
     * Cached for 24 hours.
     */
    public function getCatalogProducts(): array
    {
        return Cache::remember('printful.catalog.products', 86400, function () {
            $response = $this->request('GET', '/products');
            return $response['result'] ?? [];
        });
    }

    /**
     * Get detailed info for a single catalog product including all variants.
     */
    public function getCatalogProduct(int $productId): array
    {
        return Cache::remember("printful.catalog.product.{$productId}", 86400, function () use ($productId) {
            $response = $this->request('GET', "/products/{$productId}");
            return $response['result'] ?? [];
        });
    }

    /**
     * Get Printful product categories.
     * Cached for 24 hours.
     */
    public function getCatalogCategories(): array
    {
        return Cache::remember('printful.catalog.categories', 86400, function () {
            $response = $this->request('GET', '/categories');
            return $response['result'] ?? [];
        });
    }

    /**
     * Get info for a single variant.
     */
    public function getVariantInfo(int $variantId): array
    {
        $response = $this->request('GET', "/products/variant/{$variantId}");
        return $response['result'] ?? [];
    }

    // ─── Sync Products (Store Products) ──────────────────────────────────

    /**
     * List all synced products in our Printful store.
     */
    public function getSyncProducts(int $limit = 100, int $offset = 0): array
    {
        $response = $this->request('GET', '/store/products', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
        return $response['result'] ?? [];
    }

    /**
     * Get a single sync product with all its sync variants.
     */
    public function getSyncProduct(int $syncProductId): array
    {
        $response = $this->request('GET', "/store/products/{$syncProductId}");
        return $response['result'] ?? [];
    }

    /**
     * Create a new sync product in our Printful store.
     */
    public function createSyncProduct(array $data): array
    {
        $response = $this->request('POST', '/store/products', $data);
        return $response['result'] ?? [];
    }

    /**
     * Update an existing sync product.
     */
    public function updateSyncProduct(int $syncProductId, array $data): array
    {
        $response = $this->request('PUT', "/store/products/{$syncProductId}", $data);
        return $response['result'] ?? [];
    }

    /**
     * Delete a sync product from our Printful store.
     */
    public function deleteSyncProduct(int $syncProductId): bool
    {
        $response = $this->request('DELETE', "/store/products/{$syncProductId}");
        return isset($response['result']);
    }

    // ─── Design Files ────────────────────────────────────────────────────

    /**
     * Upload a design file to Printful.
     */
    public function uploadFile(string $filePath): array
    {
        $response = Http::withToken($this->apiKey)
            ->attach('file', file_get_contents($filePath), basename($filePath))
            ->post("{$this->baseUrl}/files");

        if ($response->failed()) {
            Log::error('Printful file upload failed', [
                'file' => $filePath,
                'status' => $response->status(),
                'error' => $response->json(),
            ]);
            throw new \RuntimeException('File upload failed: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
        }

        return $response->json()['result'] ?? [];
    }

    /**
     * Upload a design file from a URL.
     */
    public function uploadFileFromUrl(string $url): array
    {
        $response = $this->request('POST', '/files', [
            'url' => $url,
        ]);
        return $response['result'] ?? [];
    }

    /**
     * Get file info by ID.
     */
    public function getFile(int $fileId): array
    {
        $response = $this->request('GET', "/files/{$fileId}");
        return $response['result'] ?? [];
    }

    // ─── Mockup Generation ───────────────────────────────────────────────

    /**
     * Create a mockup generation task.
     * Returns task key for polling.
     */
    public function generateMockup(int $productId, array $files, array $variantIds = []): array
    {
        $data = [
            'variant_ids' => $variantIds,
            'files' => $files,
        ];

        $response = $this->request('POST', "/mockup-generator/create-task/{$productId}", $data);
        return $response['result'] ?? [];
    }

    /**
     * Poll mockup generation task status.
     */
    public function getMockupTask(string $taskKey): array
    {
        $response = $this->request('GET', "/mockup-generator/task", [
            'task_key' => $taskKey,
        ]);
        return $response['result'] ?? [];
    }

    /**
     * Get available mockup templates for a product.
     */
    public function getMockupTemplates(int $productId): array
    {
        $response = $this->request('GET', "/mockup-generator/templates/{$productId}");
        return $response['result'] ?? [];
    }

    /**
     * Generate mockups and wait for completion (polling).
     * Returns generated mockup URLs.
     */
    public function generateAndWait(int $productId, array $files, array $variantIds = [], int $maxAttempts = 30): array
    {
        $task = $this->generateMockup($productId, $files, $variantIds);

        if (empty($task['task_key'])) {
            throw new \RuntimeException('No task key returned from mockup generation');
        }

        $taskKey = $task['task_key'];

        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(2);

            $result = $this->getMockupTask($taskKey);

            if (($result['status'] ?? '') === 'completed') {
                return $result['mockups'] ?? [];
            }

            if (($result['status'] ?? '') === 'failed') {
                throw new \RuntimeException('Mockup generation failed: ' . ($result['error'] ?? 'Unknown error'));
            }
        }

        throw new \RuntimeException('Mockup generation timed out after ' . ($maxAttempts * 2) . ' seconds');
    }

    // ─── Orders ──────────────────────────────────────────────────────────

    /**
     * Create an order in Printful from a platform order.
     */
    public function createOrder(Order $order, array $printfulItems): array
    {
        $response = $this->request('POST', '/orders', [
            'recipient' => [
                'name' => $order->shipping_address['name'] ?? $order->customer->name,
                'address1' => $order->shipping_address['street'],
                'city' => $order->shipping_address['city'],
                'state_code' => $order->shipping_address['state'],
                'country_code' => $order->shipping_address['country'] ?? 'US',
                'zip' => $order->shipping_address['zip'],
            ],
            'items' => $printfulItems,
            'external_id' => (string) $order->id,
        ]);

        return $response['result'] ?? [];
    }

    /**
     * Estimate costs for an order before placing it.
     */
    public function estimateOrderCost(array $data): array
    {
        $response = $this->request('POST', '/orders/estimate-costs', $data);
        return $response['result'] ?? [];
    }

    /**
     * Confirm a draft order for fulfillment.
     */
    public function confirmOrder(int $orderId): array
    {
        $response = $this->request('POST', "/orders/{$orderId}/confirm");
        return $response['result'] ?? [];
    }

    /**
     * Get a Printful order by ID.
     */
    public function getOrder(int $orderId): array
    {
        $response = $this->request('GET', "/orders/{$orderId}");
        return $response['result'] ?? [];
    }

    /**
     * Get a Printful order by external (our) ID.
     */
    public function getOrderByExternalId(string $externalId): array
    {
        $response = $this->request('GET', "/orders/@{$externalId}");
        return $response['result'] ?? [];
    }

    /**
     * Cancel an unfulfilled Printful order.
     */
    public function cancelOrder(int $orderId): array
    {
        $response = $this->request('DELETE', "/orders/{$orderId}");
        return $response['result'] ?? [];
    }

    // ─── Shipping ────────────────────────────────────────────────────────

    /**
     * Get shipping rates. Cached for 30 min by address+items hash.
     */
    public function getShippingRates(array $recipient, array $items): array
    {
        $cacheKey = 'printful.shipping.' . md5(json_encode([$recipient, $items]));

        return Cache::remember($cacheKey, 1800, function () use ($recipient, $items) {
            $response = $this->request('POST', '/shipping/rates', [
                'recipient' => $recipient,
                'items' => $items,
            ]);
            return $response['result'] ?? [];
        });
    }

    // ─── Tax ─────────────────────────────────────────────────────────────

    /**
     * Get tax rate for an address.
     */
    public function getTaxRate(array $address): array
    {
        $response = $this->request('POST', '/tax/rates', [
            'recipient' => $address,
        ]);
        return $response['result'] ?? [];
    }

    // ─── Webhooks ────────────────────────────────────────────────────────

    /**
     * Register webhook URL with Printful.
     */
    public function setupWebhooks(string $url, array $events = []): array
    {
        $data = ['url' => $url];
        if (!empty($events)) {
            $data['types'] = $events;
        }

        $response = $this->request('POST', '/webhooks', $data);
        return $response['result'] ?? [];
    }

    /**
     * Get current webhook configuration.
     */
    public function getWebhooks(): array
    {
        $response = $this->request('GET', '/webhooks');
        return $response['result'] ?? [];
    }

    // ─── Catalog Sync Helper ─────────────────────────────────────────────

    /**
     * Sync Printful catalog to local cache table.
     * Called by printful:sync-catalog scheduled command.
     */
    public function syncCatalogToCache(): int
    {
        $products = $this->getCatalogProducts();
        $synced = 0;

        foreach ($products as $product) {
            $details = $this->getCatalogProduct($product['id']);

            $colors = [];
            $sizes = [];
            $minPrice = PHP_FLOAT_MAX;
            $maxPrice = 0;

            foreach ($details['variants'] ?? [] as $variant) {
                $minPrice = min($minPrice, (float) ($variant['price'] ?? 0));
                $maxPrice = max($maxPrice, (float) ($variant['price'] ?? 0));

                if (!empty($variant['color'])) {
                    $colors[$variant['color']] = $variant['color_code'] ?? null;
                }
                if (!empty($variant['size'])) {
                    $sizes[] = $variant['size'];
                }
            }

            PrintfulCatalogCache::updateOrCreate(
                ['printful_product_id' => $product['id']],
                [
                    'name' => $product['title'] ?? $product['name'] ?? '',
                    'description' => $details['product']['description'] ?? null,
                    'category' => $product['type_name'] ?? null,
                    'image_url' => $product['image'] ?? null,
                    'variant_count' => count($details['variants'] ?? []),
                    'min_price' => $minPrice === PHP_FLOAT_MAX ? null : $minPrice,
                    'max_price' => $maxPrice ?: null,
                    'colors_json' => array_unique(array_filter($colors)),
                    'sizes_json' => array_values(array_unique(array_filter($sizes))),
                    'print_areas_json' => $this->extractPrintAreas($details),
                    'cached_at' => now(),
                ]
            );

            $synced++;
        }

        return $synced;
    }

    // ─── Internal Helpers ────────────────────────────────────────────────

    /**
     * Make an API request with rate limit handling.
     */
    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        $response = match (strtoupper($method)) {
            'GET' => Http::withToken($this->apiKey)->get($url, $data),
            'POST' => Http::withToken($this->apiKey)->post($url, $data),
            'PUT' => Http::withToken($this->apiKey)->put($url, $data),
            'DELETE' => Http::withToken($this->apiKey)->delete($url, $data),
        };

        // Handle rate limiting
        if ($response->status() === 429) {
            $retryAfter = (int) ($response->header('Retry-After') ?? 60);
            Log::warning('Printful rate limit hit, retrying', ['retry_after' => $retryAfter]);
            sleep(min($retryAfter, 120));

            return $this->request($method, $endpoint, $data);
        }

        if ($response->failed()) {
            $error = $response->json()['error']['message'] ?? $response->json()['message'] ?? 'Unknown error';
            Log::error('Printful API error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'error' => $error,
            ]);
            throw new \RuntimeException("Printful API error ({$response->status()}): {$error}");
        }

        return $response->json();
    }

    /**
     * Extract print area info from product details.
     */
    private function extractPrintAreas(array $productDetails): array
    {
        $printFiles = $productDetails['product']['files'] ?? [];
        $areas = [];

        foreach ($printFiles as $file) {
            if (isset($file['id'], $file['title'])) {
                $areas[] = [
                    'id' => $file['id'],
                    'title' => $file['title'],
                    'type' => $file['type'] ?? 'default',
                ];
            }
        }

        return $areas;
    }
}
