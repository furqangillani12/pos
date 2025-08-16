<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'loyalty_points'
    ];

    // Relationship with Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
