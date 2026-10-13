<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Schema;

/**
 * Applies running sales to prices. A sale takes X% off its products for the
 * customer types it was set up for; when two running sales cover the same
 * product and type, the bigger discount wins.
 */
class SaleService
{
    /** product_id => [type => percent], built once per request. */
    private static ?array $map = null;

    public static function forget(): void
    {
        self::$map = null;
    }

    /** walk-in / null / customer → customer; wholesaler → wholesale. */
    public static function type(?string $type): string
    {
        return match ($type) {
            'reseller'                => 'reseller',
            'wholesale', 'wholesaler' => 'wholesale',
            default                   => 'customer',
        };
    }

    public static function percent(?int $productId, ?string $type): float
    {
        if (!$productId) return 0.0;
        return (float) (self::map()[$productId][self::type($type)] ?? 0);
    }

    /** Price after the running sale (rounded to whole rupees), or the price unchanged. */
    public static function apply(?int $productId, float $price, ?string $type): float
    {
        $pct = self::percent($productId, $type);
        return $pct > 0 ? round($price * (1 - $pct / 100)) : $price;
    }

    /** When the sale on this product (for this type) ends, if it has an end date. */
    public static function endsAt(?int $productId, ?string $type): ?\Carbon\Carbon
    {
        if (!$productId || self::percent($productId, $type) <= 0) return null;
        $type = self::type($type);
        return Sale::running()
            ->whereHas('products', fn ($q) => $q->where('products.id', $productId))
            ->get()
            ->filter(fn ($s) => in_array($type, $s->applies_to ?? [], true))
            ->sortByDesc('discount_percent')
            ->first()?->ends_at;
    }

    private static function map(): array
    {
        if (self::$map !== null) return self::$map;
        self::$map = [];
        if (!Schema::hasTable('sales')) return self::$map;

        foreach (Sale::running()->with('products:id')->get() as $sale) {
            foreach ($sale->products as $p) {
                foreach ((array) $sale->applies_to as $type) {
                    $cur = self::$map[$p->id][$type] ?? 0;
                    self::$map[$p->id][$type] = max($cur, (float) $sale->discount_percent);
                }
            }
        }
        return self::$map;
    }
}
