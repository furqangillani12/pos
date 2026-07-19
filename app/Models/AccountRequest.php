<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountRequest extends Model
{
    protected $fillable = [
        'customer_id', 'order_id', 'type', 'amount',
        'sender_name', 'sender_bank', 'reference', 'proof_path',
        'account_title', 'account_number', 'bank_name', 'admin_proof_path',
        'status', 'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }
}
