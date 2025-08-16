<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'barcode', 'description',
        'price', 'cost_price', 'stock_quantity', 'reorder_level', 'image', 'is_active','track_inventory'
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Product Variants
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Relationship with Order Items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with Purchase Items
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Relationship with Inventory Logs
    public function inventoryLogs()
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
        return $query->where('status', 1);
    }


}
