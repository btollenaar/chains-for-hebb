@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">New CSV Import</h1>
                <p class="text-gray-600 mt-1">Upload a CSV file to import products or customers</p>
            </div>
            <div>
                <a href="{{ route('admin.imports.index') }}" class="btn-admin-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Imports
                </a>
            </div>
        </div>
    </div>

    <div class="pb-12">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('admin.imports.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Import Type -->
                    <div class="mb-6">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Import Type</label>
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-admin-teal focus:ring-admin-teal" required>
                            <option value="">Select type...</option>
                            <option value="products" {{ old('type') === 'products' ? 'selected' : '' }}>Products</option>
                            <option value="customers" {{ old('type') === 'customers' ? 'selected' : '' }}>Customers</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                        <input type="file" id="file" name="file" accept=".csv,.txt"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-admin-teal file:text-white hover:file:bg-teal-700 file:cursor-pointer"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Accepted formats: .csv, .txt (max 10MB)</p>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Template Downloads -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">
                            <i class="fas fa-info-circle mr-1"></i> Download Templates
                        </h3>
                        <p class="text-xs text-blue-700 mb-3">Download a blank CSV template with the correct column headers for your import type.</p>
                        <div class="flex gap-3 flex-wrap">
                            <a href="{{ route('admin.imports.template', 'products') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-200 rounded-md text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-download mr-1"></i> Products Template
                            </a>
                            <a href="{{ route('admin.imports.template', 'customers') }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-blue-200 rounded-md text-xs font-medium text-blue-700 hover:bg-blue-50 transition-colors duration-200">
                                <i class="fas fa-download mr-1"></i> Customers Template
                            </a>
                        </div>
                    </div>

                    <!-- Column Requirements -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">
                            <i class="fas fa-columns mr-1"></i> Column Requirements
                        </h3>
                        <div class="space-y-3 text-xs text-gray-600">
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">Products:</p>
                                <p><span class="text-red-600">Required:</span> name, price</p>
                                <p><span class="text-gray-500">Optional:</span> sku, sale_price, description, stock_quantity, category, status, weight_oz</p>
                                <p class="mt-1 text-gray-500">If SKU is provided, existing products with the same SKU will be updated.</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1">Customers:</p>
                                <p><span class="text-red-600">Required:</span> name, email</p>
                                <p><span class="text-gray-500">Optional:</span> phone, billing_street, billing_city, billing_state, billing_zip, billing_country</p>
                                <p class="mt-1 text-gray-500">Existing customers with the same email will be updated.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.imports.index') }}" class="btn-admin-secondary">Cancel</a>
                        <button type="submit" class="btn-admin-primary">
                            <i class="fas fa-upload mr-2"></i>Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
