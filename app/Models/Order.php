<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'user_id', 'order_type',
        'subtotal', 'tax', 'discount', 'delivery_charges', 'weight', 'total',
        'payment_method', 'status', 'notes', 'tax_rate',
        'dispatch_method', 'tracking_id'
    ];

    protected $attributes = [
        'subtotal' => 0,
        'tax' => 0,
        'discount' => 0,
        'delivery_charges' => 0,
        'weight' => 0, // ✅ ensure default value is set
        'total' => 0,
        'tax_rate' => 10
    ];

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED  = 'refunded';
    const STATUS_CANCELLED = 'cancelled';

    // Payment methods
    const PAYMENT_CASH   = 'cash';
    const PAYMENT_CARD   = 'card';
    const PAYMENT_MOBILE = 'mobile_money';
    const PAYMENT_MIXED  = 'mixed';

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $datePart = now()->format('Ymd'); // Example: 20250929

        $latest = static::query()
            ->where('order_number', 'like', $prefix . $datePart . '%')
            ->latest('id')
            ->first();

        $number = 1;
        if ($latest) {
            $lastNumber = (int) substr($latest->order_number, -4);
            $number = $lastNumber + 1;
        }

        $sequence = str_pad($number, 4, '0', STR_PAD_LEFT);

        return $prefix . $datePart . $sequence;
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax      = $this->subtotal * ($this->tax_rate / 100);

        // ✅ weight not directly included in total (but can be used later for shipping cost logic)
        $this->total = $this->subtotal + $this->tax + $this->delivery_charges - $this->discount;

        return $this;
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function isRefundable()
    {
        return $this->status === self::STATUS_COMPLETED &&
            $this->created_at->gt(now()->subDays(30));
    }
}
