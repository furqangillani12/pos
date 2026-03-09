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
}
