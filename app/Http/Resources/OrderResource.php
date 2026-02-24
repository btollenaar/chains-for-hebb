<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) ($this->tax_amount ?? 0),
            'shipping_cost' => (float) ($this->shipping_cost ?? 0),
            'discount' => (float) ($this->discount_amount ?? 0),
            'total' => (float) $this->total_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'fulfillment_status' => $this->fulfillment_status,
            'shipping_method' => $this->shipping_method,
            'coupon_code' => $this->coupon_code,
            'shipping_address' => $this->shipping_address,
            'tracking_number' => $this->tracking_number,
            'tracking_carrier' => $this->tracking_carrier,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name ?? 'N/A',
                        'quantity' => $item->quantity,
                        'price' => (float) ($item->unit_price ?? 0),
                        'total' => (float) ($item->subtotal ?? 0),
                        'type' => $item->item_type ? class_basename($item->item_type) : null,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
