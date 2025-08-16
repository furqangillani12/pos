<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['user_id','name', 'description'];

    // Relationship with Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
