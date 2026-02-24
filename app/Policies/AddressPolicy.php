<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\Customer;

class AddressPolicy
{
    public function update(Customer $customer, Address $address): bool
    {
        return $customer->id === $address->customer_id;
    }

    public function delete(Customer $customer, Address $address): bool
    {
        return $customer->id === $address->customer_id;
    }
}
