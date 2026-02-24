<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?Customer $customer): bool
    {
        // Everyone (including guests) can view products
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?Customer $customer, Product $product): bool
    {
        // Everyone (including guests) can view individual products
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Customer $customer): bool
    {
        // Only admin can create products
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Customer $customer, Product $product): bool
    {
        // Only admin can update products
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Customer $customer, Product $product): bool
    {
        // Only admin can delete products
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Customer $customer, Product $product): bool
    {
        // Only admin can restore products
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Customer $customer, Product $product): bool
    {
        // Only admin can force delete products
        return $customer->isAdmin();
    }
}
