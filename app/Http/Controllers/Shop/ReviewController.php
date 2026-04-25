<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:191',
            'body'   => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($data, $product) {
            ProductReview::create([
                'product_id'  => $product->id,
                'customer_id' => Auth::guard('customer')->id(),
                'rating'      => $data['rating'],
                'title'       => $data['title'] ?? null,
                'body'        => $data['body'] ?? null,
                'status'      => 'approved',
            ]);

            // Recompute aggregates
            $agg = ProductReview::where('product_id', $product->id)
                ->where('status', 'approved')
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count')
                ->first();
            $product->update([
                'avg_rating'   => round((float) $agg->avg_rating, 2),
                'review_count' => (int) $agg->review_count,
            ]);
        });

        return back()->with('shop_success', 'Thanks for your review!');
    }
}
