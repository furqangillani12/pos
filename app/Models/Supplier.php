<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'company_name'
    ];

    // Relationship with Purchases
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
