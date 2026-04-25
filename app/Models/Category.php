<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'parent_id',
        'name', 'slug', 'description',
        'photo', 'sort_order', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (Category $c) {
            if (empty($c->slug) && $c->name) {
                $base = Str::slug($c->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $c->id ?? 0)->exists()) {
                    $slug = $base . '-' . ++$i;
                }
                $c->slug = $slug;
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeRoots($q)
    {
        return $q->whereNull('parent_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
