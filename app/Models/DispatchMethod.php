<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchMethod extends Model
{
    protected $fillable = ['name', 'has_tracking', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'has_tracking' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function deliverySlabs()
    {
        return $this->hasMany(DeliveryChargeSlab::class)->orderBy('min_weight');
    }
}
