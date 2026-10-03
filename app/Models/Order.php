<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_id','customer_type', 'user_id', 'order_type',
        'subtotal', 'tax', 'discount', 'discount_label', 'delivery_charges', 'packing_total', 'weight', 'weight_unit', 'total',
        'payment_method', 'payment_status', 'status', 'status_note', 'notes', 'tax_rate', 'tax_type', 'reorder_of_order_id',
        'dispatch_method', 'tracking_id', 'receipt_token','credit_status',
        'credit_ledger_id',
        'credit_due_date',
        'credit_paid_amount',
        'credit_remaining_amount',
        'paid_amount',
        'khata_paid',
        'previous_balance',
        'balance_amount',
        'branch_id',
        // ── Storefront / online order fields (added by 2026_04_27 migrations) ──
        'order_source',
        'customer_email',
        'shipping_first_name', 'shipping_last_name', 'shipping_phone',
        'shipping_address1', 'shipping_address2',
        'shipping_city', 'shipping_country', 'shipping_post_code',
        'shipping_province', 'shipping_district', 'shipping_tehsil',
        'from_name', 'from_phone', 'from_address', 'dispatch_media_path',
        'dispatch_cod_amount', 'dispatch_remarks', 'dispatch_pieces',
        'coupon_code', 'coupon_discount',
        'points_redeemed', 'points_discount',
        'online_payment_status', 'online_payment_ref',
        'payment_proof_path', 'payment_sender_name', 'payment_sender_bank', 'payment_sender_amount',
        'order_notes_customer',
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

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at')->orderBy('id');
    }

    /** Append a status event to this order's tracking history. */
    public function recordStatus(string $status, ?string $note = null): void
    {
        $this->statusHistory()->create(['status' => $status, 'note' => $note]);
    }

    /** The returned order this one was re-ordered from (#1e). */
    public function reorderOf(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'reorder_of_order_id');
    }

    /** The re-order placed from this (returned) order (#1e). */
    public function reorderedAs(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Order::class, 'reorder_of_order_id');
    }

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED  = 'refunded';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Statuses whose bill no longer counts on the customer's khata. Cancelled and
     * returned orders are fully reversed (stock back, khata reversed); a refunded
     * POS order stays on the khata and its Refund rows credit it instead.
     */
    const KHATA_EXCLUDED_STATUSES = ['cancelled', 'returned'];

    /**
     * What was paid against this bill at the counter / on the bill itself
     * (excludes khata payments allocated to it). Legacy pre-khata orders were
     * stored with paid_amount = 0 AND balance_amount = 0, meaning "fully paid".
     */
    public function counterPaid(): float
    {
        if ((float) $this->paid_amount == 0.0 && (float) $this->balance_amount == 0.0 && (float) ($this->khata_paid ?? 0) == 0.0) {
            return (float) $this->total;
        }
        return (float) $this->paid_amount;
    }

    /** This bill's effect on the customer's khata: total minus counter payment. */
    public function khataNet(): float
    {
        return round((float) $this->total - $this->counterPaid(), 2);
    }

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
     * Generate order number based on branch code and per-branch sequence.
     */
    public static function generateOrderNumber($branchId = null)
    {
        $branch = null;
        if ($branchId && $branchId !== 'all') {
            $branch = Branch::find($branchId);
        }

        $prefix = $branch && $branch->code ? $branch->code : 'ASM';
        $startNumber = $branch && $branch->order_start_number ? $branch->order_start_number : 1272;

        $latest = static::query()
            ->where('order_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $number = $startNumber;
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
        // Subtract BOTH the coupon/discount and any redeemed-points discount, so
        // the total always reflects points (matches the storefront checkout).
        $afterDiscount  = max(0, $this->subtotal - $this->discount - (float) ($this->points_discount ?? 0));
        $this->tax      = $afterDiscount * ($this->tax_rate / 100);
        $this->total    = $afterDiscount + $this->tax + $this->delivery_charges;

        return $this;
    }

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

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
     * Dynamically compute the customer's running balance BEFORE this order.
     * This is more reliable than the stored previous_balance which can become
     * stale when orders are edited out of sequence.
     */
    public function computePreviousBalance(): float
    {
        // Same khata rows as the customer statement (bills, payments, payouts,
        // offsets, refunds), so receipts and statements always agree.
        return \App\Services\KhataService::balanceBefore($this);
    }

    /**
     * Check if order is refundable
     */
    public function isRefundable(): bool
    {
        // Cancelled orders cannot be returned
        if ($this->status === 'cancelled') return false;
        // Show return button as long as there's remaining amount to return
        return $this->remainingRefundable() > 0;
    }

    public function remainingRefundable(): float
    {
        $alreadyRefunded = $this->refunds()->where('status', 'completed')->sum('amount');
        return max(0, $this->total - $alreadyRefunded);
    }
}