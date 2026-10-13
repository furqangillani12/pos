<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A product offered as a gift (website gift picker before checkout, and the POS). */
class Gift extends Model
{
    protected $fillable = ['branch_id', 'product_id', 'price', 'is_active', 'sort_order'];

    protected $casts = [
        'price'     => 'float',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Active gifts whose product is active and (if stock is tracked) in stock at its branch. */
    public static function available()
    {
        return static::with('product')
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->filter(function (Gift $g) {
                $p = $g->product;
                if (!$p || !$p->is_active || $p->has_variants) return false;
                return !$p->track_inventory || $p->getStockForBranch($p->branch_id) >= 1;
            })
            ->values();
    }

    /**
     * Gift rules: [['min' => 2000, 'gifts' => 1], ['min' => 5000, 'gifts' => 3]], lowest first.
     * Stored in the settings table under "gift_tiers".
     */
    public static function tiers(): array
    {
        $rows = json_decode((string) Setting::get('gift_tiers', '[]'), true) ?: [];
        return collect($rows)
            ->map(fn ($r) => ['min' => (float) ($r['min'] ?? 0), 'gifts' => (int) ($r['gifts'] ?? 0)])
            ->filter(fn ($r) => $r['gifts'] > 0)
            ->sortBy('min')->values()->all();
    }

    /** How many gifts an order of this amount may pick. No rules set = 1 gift on every order. */
    public static function allowedFor(float $amount): int
    {
        $tiers = self::tiers();
        if (!$tiers) return 1;
        $n = 0;
        foreach ($tiers as $t) {
            if ($amount >= $t['min']) $n = $t['gifts'];
        }
        return $n;
    }

    /** The next rule above this amount, to tell the customer "spend Rs X more for N gifts". */
    public static function nextTier(float $amount): ?array
    {
        foreach (self::tiers() as $t) {
            if ($amount < $t['min'] && $t['gifts'] > self::allowedFor($amount)) return $t;
        }
        return null;
    }
}
