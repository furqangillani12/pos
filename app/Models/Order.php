<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id', 'user_id', 'order_type',
        'subtotal', 'tax', 'discount', 'total',
        'payment_method', 'status', 'notes', 'tax_rate'
    ];

    protected $attributes = [
        'subtotal' => 0,
        'tax' => 0,
        'discount' => 0,
        'total' => 0,
        'tax_rate' => 10
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CANCELLED = 'cancelled';

    // Payment methods
    const PAYMENT_CASH = 'cash';
    const PAYMENT_CARD = 'card';
    const PAYMENT_MOBILE = 'mobile_money';
    const PAYMENT_MIXED = 'mixed';

    public static function generateOrderNumber()
    {
        $prefix = 'ORD-';
        $date = now()->format('Ymd');
        $latest = static::query()
            ->where('order_number', 'like', $prefix.$date.'%')
            ->latest('id')
            ->first();

        $number = $latest ? ((int) substr($latest->order_number, -4) + 1) : 1;

        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $this->tax = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax - $this->discount;
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
