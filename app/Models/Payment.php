<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'amount', 'method', 'reference', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
