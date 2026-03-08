<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id','customer_type', 'user_id', 'order_type',
        'subtotal', 'tax', 'discount', 'delivery_charges', 'weight', 'total',
        'payment_method', 'status', 'notes', 'tax_rate',
        'dispatch_method', 'tracking_id', 'receipt_token','credit_status',
    'credit_ledger_id',
    'credit_due_date',
    'credit_paid_amount',
    'credit_remaining_amount',
    'paid_amount',
    'previous_balance',
    'balance_amount',
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

    // Add this for auto-casting
    protected $appends = ['receipt_url'];

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

    /**
     * Boot method to auto-generate receipt token
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->receipt_token)) {
                $order->receipt_token = Str::random(32);
            }
        });
    }
    

    /**
     * Generate receipt URL attribute
     */
    public function getReceiptUrlAttribute()
    {
        return route('public.receipt.show', $this->receipt_token);
    }

    /**
     * Generate order number
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ASM';

        $latest = static::query()
            ->where('order_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = 1272;
        if ($latest) {
            $lastNumber = (int) str_replace($prefix, '', $latest->order_number);
            if ($lastNumber >= $number) {
                $number = $lastNumber + 1;
            }
        }

        return $prefix . $number;
    }

    /**
     * Calculate order totals
     */
    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('total_price');
        $afterDiscount  = $this->subtotal - $this->discount;
        $this->tax      = $afterDiscount * ($this->tax_rate / 100);
        $this->total    = $afterDiscount + $this->tax + $this->delivery_charges;

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

    /**
     * Check if order is refundable
     */
    public function isRefundable()
    {
        return $this->status === self::STATUS_COMPLETED &&
            $this->created_at->gt(now()->subDays(30));
    }
}