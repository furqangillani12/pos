<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id', 'invoice_number', 'total_amount', 'paid_amount', 'payment_status', 'purchase_date', 'notes'
    ];

    // Relationship with Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relationship with Purchase Items
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
