<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points',
        'customer_type', // NEW: customer type field
    ];

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

    /**
     * Get human readable type
     */
    public function getTypeLabelAttribute(): string
    {
        switch ($this->customer_type) {
            case 'reseller':
                return 'Reseller';
            case 'wholesaler':
                return 'Wholesaler';
            default:
                return 'Customer';
        }
    }
}
