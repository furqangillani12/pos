<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $heroBanners = Banner::active()->position('hero')->orderBy('sort_order')->get();
        $midBanners  = Banner::active()->position('mid')->orderBy('sort_order')->limit(10)->get();

        $featuredCategories = Category::active()->where('is_featured', true)
            ->orderBy('sort_order')->limit(8)->get();

        // Fall back to any active categories when none are flagged "featured",
        // so the "Shop by category" section is always populated.
        if ($featuredCategories->isEmpty()) {
            $featuredCategories = Category::active()
                ->orderBy('sort_order')->orderBy('name')->limit(8)->get();
        }

        // New arrivals first — its ids are excluded from the fallback sections
        // below so the three product rows never show the same items.
        $newArrivals = Product::onWebsite()
            ->with('category', 'brand')
            ->orderByDesc('created_at')->limit(8)->get();
        $usedIds = $newArrivals->pluck('id')->all();

        // Featured / best picks — curated when flagged, else a distinct set.
        $featuredProducts = Product::onWebsite()->featured()
            ->with('category', 'brand')
            ->orderByDesc('id')->limit(8)->get();
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::onWebsite()
                ->whereNotIn('id', $usedIds)
                ->with('category', 'brand')
                ->inRandomOrder()->limit(8)->get();
        }
        $usedIds = array_merge($usedIds, $featuredProducts->pluck('id')->all());

        // Top rated — real ratings when present, else a distinct best-available set.
        $bestRated = Product::onWebsite()
            ->where('avg_rating', '>=', 4)
            ->with('category', 'brand')
            ->orderByDesc('avg_rating')->orderByDesc('review_count')->limit(8)->get();
        if ($bestRated->isEmpty()) {
            $bestRated = Product::onWebsite()
                ->whereNotIn('id', $usedIds)
                ->with('category', 'brand')
                ->orderByDesc('review_count')->orderByDesc('id')
                ->limit(8)->get();
        }

        $brands = Brand::where('is_active', true)->where('is_featured', true)
            ->orderBy('sort_order')->limit(8)->get();

        if ($brands->isEmpty()) {
            $brands = Brand::where('is_active', true)->orderBy('sort_order')->orderBy('name')->limit(8)->get();
        }

        return view('shop.pages.home', compact(
            'heroBanners', 'midBanners', 'featuredCategories',
            'featuredProducts', 'newArrivals', 'bestRated', 'brands'
        ));
    }
}
