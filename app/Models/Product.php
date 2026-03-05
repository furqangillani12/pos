<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'barcode',
        'description',
        'price', 
        'sale_price',
        'resale_price',
        'wholesale_price',
        'cost_price',
        'weight',
        'stock_quantity',
        'reorder_level',
        'image',
        'is_active',
        'track_inventory',
        'rank' // Added rank field for box placement
    ];

    // Relationship with Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
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
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('rank', 'like', '%' . $filters['search'] . '%'); // Added rank to search
            });
        }

        if (!empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }
        
        // Optional: Add filter by rank range
        if (!empty($filters['rank'])) {
            $query->where('rank', $filters['rank']);
        }
        
        if (!empty($filters['min_rank'])) {
            $query->where('rank', '>=', $filters['min_rank']);
        }
        
        if (!empty($filters['max_rank'])) {
            $query->where('rank', '<=', $filters['max_rank']);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope to order products by rank
     */
    public function scopeOrderByRank($query, $direction = 'asc')
    {
        return $query->orderBy('rank', $direction);
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

    /**
     * Get the box/placement information based on rank
     */
    public function getBoxPlacementAttribute()
    {
        if (empty($this->rank)) {
            return 'Not assigned';
        }
        
        return "Box/Position: {$this->rank}";
    }
  
    /**
     * Get formatted weight display
     */
    public function getFormattedWeightAttribute()
    {
        if (is_null($this->weight)) {
            return 'N/A';
        }
        
        $weightInGrams = $this->weight * 1000;
        
        // If weight is 1 kg or more, show in kg
        if ($this->weight >= 1) {
            // Remove trailing zeros
            $kg = rtrim(rtrim(number_format($this->weight, 3, '.', ''), '0'), '.');
            return $kg . ' kg';
        }
        
        // If less than 1 kg, show in grams
        return number_format($weightInGrams, 0) . ' g';
    }

    /**
     * Get weight in grams
     */
    public function getWeightInGramsAttribute()
    {
        if (is_null($this->weight)) {
            return null;
        }
        
        return $this->weight * 1000;
    }

     public function getUnitDisplayAttribute()
    {
        if ($this->unit) {
            return $this->unit->abbreviation ?: $this->unit->name;
        }
        return 'N/A';
    }

    public function getNameWithUnitAttribute()
    {
        $unitDisplay = $this->unit_display;
        return $this->name . ($unitDisplay !== 'N/A' ? " ({$unitDisplay})" : '');
    }
}
