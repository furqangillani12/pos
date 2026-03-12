<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'branch_id', 'name', 'email', 'phone', 'address', 'company_name'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Relationship with Purchases
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function getTotalDueAttribute(): float
    {
        $totalPurchased = $this->purchases()->sum('total_amount');
        $totalPaidOnPurchases = $this->purchases()->sum('paid_amount');
        // Linked payments are already in purchases.paid_amount, so only add unlinked ones
        $unlinkedPayments = $this->payments()->whereNull('purchase_id')->sum('amount');
        return $totalPurchased - $totalPaidOnPurchases - $unlinkedPayments;
    }
}
