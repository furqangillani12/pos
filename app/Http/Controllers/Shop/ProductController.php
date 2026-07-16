<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    /**
     * Store a customer's "arrange / restock this product" request for an
     * out-of-stock item (#17). Open to guests; prefilled for logged-in customers.
     */
    public function requestItem(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'  => 'nullable|string|max:191',
            'phone' => 'required|string|max:40',
            'email' => 'nullable|email|max:191',
            'note'  => 'nullable|string|max:1000',
        ]);

        $customer = auth('customer')->user();

        ProductRequest::create([
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'customer_id'  => $customer?->id,
            'name'         => $data['name'] ?? $customer?->name,
            'phone'        => $data['phone'],
            'email'        => $data['email'] ?? $customer?->email,
            'note'         => $data['note'] ?? null,
            'status'       => 'new',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Request received — we\'ll contact you when it\'s available.']);
        }

        return back()->with('shop_success', 'Request received — we\'ll contact you when it\'s available.');
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active && $product->show_on_website, 404);

        $product->load(['category', 'brand', 'unit']);

        // ── Behaviour tracking (#12) ──────────────────────────────────────
        // Cheap atomic view counter (no model events / timestamps touched).
        Product::whereKey($product->id)->increment('views');
        $this->rememberView($product);

        $reviews = $product->approvedReviews()->with('customer:id,name')->limit(20)->get();

        $related = Product::onWebsite()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with('category', 'brand')
            ->limit(8)->get();

        // Popular picks — by real view count, excluding this product and the
        // related ones already shown, so the rows don't repeat.
        $exclude = $related->pluck('id')->push($product->id)->all();
        $popular = Product::onWebsite()
            ->whereNotIn('id', $exclude)
            ->with('category', 'brand')
            ->popular()
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

    /**
     * Record this product in the visitor's "recently viewed" list and bump the
     * interest score for its category — both kept in the session and used by the
     * home page to recommend related items (#12).
     */
    private function rememberView(Product $product): void
    {
        $recent = array_values(array_diff(Session::get('shop.recent_products', []), [$product->id]));
        array_unshift($recent, $product->id);
        Session::put('shop.recent_products', array_slice($recent, 0, 12));

        if ($product->category_id) {
            $cats = Session::get('shop.cat_interest', []);
            $cats[$product->category_id] = ($cats[$product->category_id] ?? 0) + 1;
            arsort($cats);
            Session::put('shop.cat_interest', array_slice($cats, 0, 10, true));
        }
    }
}
