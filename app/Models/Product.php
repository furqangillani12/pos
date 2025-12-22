<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'barcode',
        'description',
        'price', // you may keep this as default (sale price) or remove if not needed
        'sale_price',
        'resale_price',
        'wholesale_price',
        'cost_price',
        'weight',
        'stock_quantity',
        'reorder_level',
        'image',
        'is_active',
        'track_inventory'
    ];

    // Relationship with Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Product Variants
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Relationship with Order Items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with Purchase Items
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Relationship with Inventory Logs
    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Get price based on customer type
     */
    public function getPriceForCustomerType($customerType)
    {
        switch ($customerType) {
            case 'reseller':
                return isset($this->resale_price) ? $this->resale_price : $this->sale_price;
            case 'wholesaler':
                return isset($this->wholesale_price) ? $this->wholesale_price : $this->sale_price;
            default: // normal customer
                return isset($this->sale_price) ? $this->sale_price : $this->price;
        }
    }
}
