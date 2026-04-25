<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active && $product->show_on_website, 404);

        $product->load(['category', 'brand', 'unit']);

        $reviews = $product->approvedReviews()->with('customer:id,name')->limit(20)->get();

        $related = Product::onWebsite()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with('category', 'brand')
            ->limit(8)->get();

        return view('shop.pages.product', compact('product', 'reviews', 'related'));
    }
}
