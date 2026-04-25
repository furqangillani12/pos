<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'phone',
        'address',
        'barcode',
        'loyalty_points',
        'customer_type',
        'credit_enabled',
        'credit_limit',
        'current_balance',
        'credit_due_days',
        'credit_start_date',
        'linked_supplier_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The supplier record that represents the SAME real-world party as this customer.
     * Used for offsetting A/R against A/P when one person is both customer and supplier.
     */
    public function linkedSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'linked_supplier_id');
    }

    public static function generateBarcode()
    {
        $prefix = 'CUST';
        do {
            $number = rand(100000, 999999);
            $barcode = $prefix . $number;
        } while (self::where('barcode', $barcode)->exists());
        
        return $barcode;
    }

    // In App\Models\Customer.php
    public function setLoyaltyPointsAttribute($value)
    {
        // Convert to integer, default to 0 if empty
        $this->attributes['loyalty_points'] = $value === '' || $value === null ? 0 : (int) $value;
    }

    // Relationship with Orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('customer_type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    /**
     * Get human readable type
     */
    public function getTypeLabelAttribute(): string
    {
        switch ($this->customer_type) {
            case 'reseller':
                return 'Reseller';
            case 'wholesale':
                return 'Wholesale';
            default:
                return 'Customer';
        }
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->type_label . ')';
    }


     public function creditLedger()
    {
        return $this->hasOne(CreditLedger::class);
    }

    /**
     * Relationship with Credit Transactions
     */
    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Check if customer has sufficient credit limit
     */
    public function hasSufficientCredit($amount): bool
    {
        if (!$this->credit_enabled) {
            return false;
        }
        
        $availableCredit = $this->credit_limit - $this->current_balance;
        return $amount <= $availableCredit;
    }

    /**
     * Get available credit limit
     */
    public function getAvailableCreditAttribute(): float
    {
        if (!$this->credit_enabled) {
            return 0;
        }
        
        return $this->credit_limit - $this->current_balance;
    }

    /**
     * Get credit status badge
     */
    public function getCreditStatusAttribute(): string
    {
        if (!$this->credit_enabled) {
            return 'Not Enabled';
        }
        
        $percentage = ($this->current_balance / $this->credit_limit) * 100;
        
        if ($percentage >= 90) {
            return 'Critical';
        } elseif ($percentage >= 70) {
            return 'High';
        } elseif ($percentage >= 50) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }
}