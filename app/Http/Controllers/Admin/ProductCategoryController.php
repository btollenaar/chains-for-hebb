<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductCategory;

class ProductCategoryController extends AbstractCategoryController
{
    protected function getModelClass(): string
    {
        return ProductCategory::class;
    }

    protected function getItemType(): string
    {
        return 'product';
    }

    protected function getItemsRelationship(): string
    {
        return 'products';
    }

    protected function getTableName(): string
    {
        return 'product_categories';
    }

    protected function getImageStoragePath(): string
    {
        return 'categories/products';
    }

    protected function getCacheKey(): string
    {
        return 'navigation.product_categories';
    }

    protected function getViewPath(): string
    {
        return 'admin.products.categories';
    }

    protected function getRouteName(): string
    {
        return 'admin.products.categories';
    }
}
