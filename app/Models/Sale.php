<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** Customer types a sale can apply to => label. */
    public const TYPES = ['customer' => 'Walk-in', 'reseller' => 'Reseller', 'wholesale' => 'Wholesale'];

    protected $fillable = ['branch_id', 'name', 'discount_percent', 'applies_to', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'discount_percent' => 'float',
        'applies_to'       => 'array',
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'is_active'        => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(fn () => \App\Services\SaleService::forget());
        static::deleted(fn () => \App\Services\SaleService::forget());
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_product');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /** Running right now: switched on and inside its dates. */
    public function scopeRunning($q)
    {
        return $q->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    /** running / scheduled / ended / off */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) return 'off';
        if ($this->starts_at && $this->starts_at->isFuture()) return 'scheduled';
        if ($this->ends_at && $this->ends_at->isPast()) return 'ended';
        return 'running';
    }

    public function getAppliesToLabelAttribute(): string
    {
        return collect($this->applies_to ?? [])->map(fn ($t) => self::TYPES[$t] ?? $t)->implode(', ');
    }
}
