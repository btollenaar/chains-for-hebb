<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'customer_id',
        'order_number',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'shipping_method',
        'estimated_weight_oz',
        'coupon_id',
        'coupon_code',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_intent_id',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'fulfillment_status',
        'fulfillment_provider',
        'fulfillment_order_id',
        'billing_address',
        'shipping_address',
        'notes',
        'admin_notes',
        'review_request_sent_at',
        'tracking_number',
        'tracking_carrier',
        'shipped_at',
        'delivered_at',
        'post_purchase_email_sent_at',
        'loyalty_points_redeemed',
        'loyalty_discount',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'loyalty_discount' => 'decimal:2',
        'estimated_weight_oz' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    // Accessors
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCompletedAttribute()
    {
        return $this->fulfillment_status === 'completed';
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('fulfillment_status', 'completed');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Helper methods
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount - $this->loyalty_discount;
        $this->save();
    }

    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->fulfillment_status = 'completed';
        $this->save();
    }

    public function cancel()
    {
        $this->fulfillment_status = 'cancelled';
        $this->save();
    }

    public function markAsShipped(?string $trackingNumber = null, ?string $carrier = null)
    {
        $this->fulfillment_status = 'shipped';
        $this->shipped_at = now();
        if ($trackingNumber) {
            $this->tracking_number = $trackingNumber;
        }
        if ($carrier) {
            $this->tracking_carrier = $carrier;
        }
        $this->save();
    }

    public function markAsDelivered()
    {
        $this->fulfillment_status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    /**
     * Get the tracking URL for the carrier
     */
    public function getTrackingUrlAttribute(): ?string
    {
        if (!$this->tracking_number || !$this->tracking_carrier) {
            return null;
        }

        $urls = [
            'usps' => 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $this->tracking_number,
            'ups' => 'https://www.ups.com/track?tracknum=' . $this->tracking_number,
            'fedex' => 'https://www.fedex.com/fedextrack/?trknbr=' . $this->tracking_number,
            'dhl' => 'https://www.dhl.com/en/express/tracking.html?AWB=' . $this->tracking_number,
        ];

        return $urls[strtolower($this->tracking_carrier)] ?? null;
    }

    /**
     * Get available carrier options
     */
    public static function carrierOptions(): array
    {
        return [
            'usps' => 'USPS',
            'ups' => 'UPS',
            'fedex' => 'FedEx',
            'dhl' => 'DHL',
            'other' => 'Other',
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }
}
