<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Shared shape of the managed size / colour lists. */
abstract class ProductOption extends Model
{
    protected $fillable = ['name', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }
}
