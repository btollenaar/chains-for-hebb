<?php

namespace App\Services;

use App\Models\CsvImport;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CsvImportService
{
    /**
     * Import products from CSV file.
     */
    public function importProducts(CsvImport $import): void
    {
        $filePath = Storage::path($import->filename);

        if (!file_exists($filePath)) {
            $import->addError(0, 'CSV file not found on disk.');
            $import->markAsFailed();
            return;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $import->addError(0, 'Unable to open CSV file.');
            $import->markAsFailed();
            return;
        }

        // Read header row
        $headers = fgetcsv($handle);
        if ($headers === false || empty($headers)) {
            fclose($handle);
            $import->addError(0, 'CSV file is empty or has no header row.');
            $import->markAsFailed();
            return;
        }

        // Normalize headers (trim whitespace, lowercase)
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        // Validate required columns exist
        $requiredColumns = ['name', 'price'];
        $missingColumns = array_diff($requiredColumns, $headers);
        if (!empty($missingColumns)) {
            fclose($handle);
            $import->addError(0, 'Missing required columns: ' . implode(', ', $missingColumns));
            $import->markAsFailed();
            return;
        }

        $rowNumber = 1; // Start at 1 (header is row 0)
        $chunk = [];
        $chunkSize = 100;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            // Map CSV columns to associative array
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $row[$index] ?? null;
            }

            $this->processProductRow($import, $rowNumber, $data);
        }

        fclose($handle);
        $import->markAsCompleted();
    }

    /**
     * Process a single product row from CSV.
     */
    protected function processProductRow(CsvImport $import, int $rowNumber, array $data): void
    {
        try {
            // Validate row data
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'sku' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'stock_quantity' => 'nullable|integer|min:0',
                'category' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:active,draft,archived',
                'weight_oz' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $import->addError($rowNumber, implode('; ', $errors));
                $import->incrementProcessed();
                return;
            }

            $validated = $validator->validated();

            // Build product attributes
            $productData = [
                'name' => $validated['name'],
                'price' => $validated['price'],
                'slug' => Str::slug($validated['name']),
                'status' => $validated['status'] ?? 'active',
            ];

            if (!empty($validated['description'])) {
                $productData['description'] = $validated['description'];
            }

            if (isset($validated['stock_quantity'])) {
                $productData['stock_quantity'] = (int) $validated['stock_quantity'];
            }

            if (!empty($validated['weight_oz'])) {
                $productData['weight_oz'] = $validated['weight_oz'];
            }

            if (!empty($validated['sale_price'])) {
                $productData['sale_price'] = $validated['sale_price'];
            }

            // Upsert by SKU if provided, otherwise create new
            if (!empty($validated['sku'])) {
                $productData['sku'] = $validated['sku'];
                $product = Product::updateOrCreate(
                    ['sku' => $validated['sku']],
                    $productData
                );
            } else {
                // Ensure unique slug
                $baseSlug = $productData['slug'];
                $counter = 1;
                while (Product::where('slug', $productData['slug'])->exists()) {
                    $productData['slug'] = $baseSlug . '-' . $counter++;
                }
                $product = Product::create($productData);
            }

            // Assign category if provided
            if (!empty($validated['category'])) {
                $category = ProductCategory::where('name', $validated['category'])->first();
                if ($category) {
                    $product->update(['category_id' => $category->id]);
                    // Also assign via pivot if not already
                    if (!$product->categories()->where('product_category_id', $category->id)->exists()) {
                        $product->categories()->attach($category->id, ['is_primary' => true, 'display_order' => 0]);
                    }
                }
            }

            $import->incrementSuccess();

        } catch (\Exception $e) {
            Log::error('CSV product import row failed', [
                'import_id' => $import->id,
                'row' => $rowNumber,
                'error' => $e->getMessage(),
            ]);
            $import->addError($rowNumber, 'Unexpected error: ' . $e->getMessage());
            $import->incrementProcessed();
        }
    }

    /**
     * Import customers from CSV file.
     */
    public function importCustomers(CsvImport $import): void
    {
        $filePath = Storage::path($import->filename);

        if (!file_exists($filePath)) {
            $import->addError(0, 'CSV file not found on disk.');
            $import->markAsFailed();
            return;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $import->addError(0, 'Unable to open CSV file.');
            $import->markAsFailed();
            return;
        }

        // Read header row
        $headers = fgetcsv($handle);
        if ($headers === false || empty($headers)) {
            fclose($handle);
            $import->addError(0, 'CSV file is empty or has no header row.');
            $import->markAsFailed();
            return;
        }

        // Normalize headers
        $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

        // Validate required columns
        $requiredColumns = ['name', 'email'];
        $missingColumns = array_diff($requiredColumns, $headers);
        if (!empty($missingColumns)) {
            fclose($handle);
            $import->addError(0, 'Missing required columns: ' . implode(', ', $missingColumns));
            $import->markAsFailed();
            return;
        }

        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            // Map CSV columns to associative array
            $data = [];
            foreach ($headers as $index => $header) {
                $data[$header] = $row[$index] ?? null;
            }

            $this->processCustomerRow($import, $rowNumber, $data);
        }

        fclose($handle);
        $import->markAsCompleted();
    }

    /**
     * Process a single customer row from CSV.
     */
    protected function processCustomerRow(CsvImport $import, int $rowNumber, array $data): void
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'billing_street' => 'nullable|string|max:255',
                'billing_city' => 'nullable|string|max:255',
                'billing_state' => 'nullable|string|max:2',
                'billing_zip' => 'nullable|string|max:10',
                'billing_country' => 'nullable|string|max:2',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $import->addError($rowNumber, implode('; ', $errors));
                $import->incrementProcessed();
                return;
            }

            $validated = $validator->validated();

            $customerData = [
                'name' => $validated['name'],
            ];

            if (!empty($validated['phone'])) {
                $customerData['phone'] = $validated['phone'];
            }
            if (!empty($validated['billing_street'])) {
                $customerData['billing_street'] = $validated['billing_street'];
            }
            if (!empty($validated['billing_city'])) {
                $customerData['billing_city'] = $validated['billing_city'];
            }
            if (!empty($validated['billing_state'])) {
                $customerData['billing_state'] = $validated['billing_state'];
            }
            if (!empty($validated['billing_zip'])) {
                $customerData['billing_zip'] = $validated['billing_zip'];
            }
            if (!empty($validated['billing_country'])) {
                $customerData['billing_country'] = $validated['billing_country'];
            }

            // Upsert by email
            Customer::updateOrCreate(
                ['email' => $validated['email']],
                $customerData
            );

            $import->incrementSuccess();

        } catch (\Exception $e) {
            Log::error('CSV customer import row failed', [
                'import_id' => $import->id,
                'row' => $rowNumber,
                'error' => $e->getMessage(),
            ]);
            $import->addError($rowNumber, 'Unexpected error: ' . $e->getMessage());
            $import->incrementProcessed();
        }
    }

    /**
     * Generate template headers for a given import type.
     */
    public function generateTemplate(string $type): array
    {
        return match ($type) {
            'products' => ['name', 'sku', 'price', 'sale_price', 'description', 'stock_quantity', 'category', 'status', 'weight_oz'],
            'customers' => ['name', 'email', 'phone', 'billing_street', 'billing_city', 'billing_state', 'billing_zip', 'billing_country'],
            default => [],
        };
    }
}
