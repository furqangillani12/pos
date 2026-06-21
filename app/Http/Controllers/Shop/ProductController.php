<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Package;
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

        // Popular picks — most reviewed / best rated, excluding this product and
        // the related ones already shown, so the rows don't repeat.
        $exclude = $related->pluck('id')->push($product->id)->all();
        $popular = Product::onWebsite()
            ->whereNotIn('id', $exclude)
            ->with('category', 'brand')
            ->orderByDesc('review_count')->orderByDesc('avg_rating')->orderByDesc('id')
            ->limit(8)->get();

        // Packages (built in POS) that include this product — shown as deals so the
        // page feels rich. Falls back to any active packages when none match.
        $packages = Package::active()
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->with('items.product')
            ->limit(4)->get();
        if ($packages->isEmpty()) {
            $packages = Package::active()->with('items.product')
                ->latest('id')->limit(3)->get();
        }
        // Drop packages whose items are all missing/empty.
        $packages = $packages->filter(fn ($p) => $p->items->isNotEmpty())->values();

        return view('shop.pages.product', compact('product', 'reviews', 'related', 'popular', 'packages'));
    }
}
