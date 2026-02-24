<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Customer $customer): bool
    {
        // All authenticated users can view orders (filtered by ownership in controller)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Customer $customer, Order $order): bool
    {
        // Admin can view all orders
        if ($customer->isAdmin()) {
            return true;
        }

        // Staff can view all orders
        if ($customer->isStaff()) {
            return true;
        }

        // Customers can only view their own orders
        return $order->customer_id === $customer->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Customer $customer): bool
    {
        // Anyone authenticated can create orders
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Customer $customer, Order $order): bool
    {
        // Admin can update all orders
        if ($customer->isAdmin()) {
            return true;
        }

        // Staff can update all orders
        if ($customer->isStaff()) {
            return true;
        }

        // Customers cannot update orders (only cancel)
        return false;
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(Customer $customer, Order $order): bool
    {
        // Admin can cancel any order
        if ($customer->isAdmin()) {
            return true;
        }

        // Customers can only cancel their own pending/processing orders
        if ($order->customer_id === $customer->id &&
            in_array($order->fulfillment_status, ['pending', 'processing'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Customer $customer, Order $order): bool
    {
        // Only admin can delete orders
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Customer $customer, Order $order): bool
    {
        // Only admin can restore orders
        return $customer->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Customer $customer, Order $order): bool
    {
        // Only admin can force delete orders
        return $customer->isAdmin();
    }
}
