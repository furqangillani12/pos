<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One colour × size combination of a product, with its own prices, stock
 * (held at the product's branch), barcode and status. A blank price falls back
 * to the product's price for that tier.
 */
class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'color', 'size', 'barcode',
        'sale_price', 'resale_price', 'wholesale_price',
        'stock', 'is_active', 'sort_order',
        'base_price', 'weight', 'reorder_level', 'images',
    ];

    protected $casts = [
        'sale_price'      => 'decimal:2',
        'resale_price'    => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'stock'           => 'decimal:2',
        'is_active'       => 'boolean',
        'base_price'      => 'decimal:2',
        'images'          => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** "Black / 54", "Black", or "54". */
    public function getLabelAttribute(): string
    {
        return implode(' / ', array_values(array_filter([$this->color, $this->size], fn ($v) => $v !== null && $v !== '')));
    }

    /** Price for a customer type (customer / walkin / reseller / wholesale). */
    public function priceFor(?string $customerType): float
    {
        $p = $this->product;
        $retail = (float) ($this->sale_price ?? 0) ?: (float) ($p->sale_price ?: $p->price ?: 0);
        return match ($customerType) {
            'reseller'               => (float) ($this->resale_price ?? 0) ?: (float) ($p->resale_price ?? 0) ?: $retail,
            'wholesale', 'wholesaler' => (float) ($this->wholesale_price ?? 0) ?: (float) ($p->wholesale_price ?? 0) ?: $retail,
            default                  => $retail,
        };
    }

    /** Retail (walk-in) price of this variant. */
    public function retailPrice(): float
    {
        return $this->priceFor('customer');
    }

    public function inStock(): bool
    {
        return !$this->product?->track_inventory || (float) $this->stock > 0;
    }
}
