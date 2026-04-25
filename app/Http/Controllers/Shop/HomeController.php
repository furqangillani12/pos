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
        $midBanners  = Banner::active()->position('mid')->orderBy('sort_order')->limit(2)->get();

        $featuredCategories = Category::active()->where('is_featured', true)
            ->orderBy('sort_order')->limit(8)->get();

        $featuredProducts = Product::onWebsite()->featured()
            ->with('category', 'brand')
            ->orderByDesc('id')->limit(8)->get();

        $newArrivals = Product::onWebsite()
            ->with('category', 'brand')
            ->orderByDesc('created_at')->limit(8)->get();

        $bestRated = Product::onWebsite()
            ->where('avg_rating', '>=', 4)
            ->with('category', 'brand')
            ->orderByDesc('avg_rating')->orderByDesc('review_count')->limit(8)->get();

        $brands = Brand::where('is_active', true)->where('is_featured', true)
            ->orderBy('sort_order')->limit(8)->get();

        return view('shop.pages.home', compact(
            'heroBanners', 'midBanners', 'featuredCategories',
            'featuredProducts', 'newArrivals', 'bestRated', 'brands'
        ));
    }
}
